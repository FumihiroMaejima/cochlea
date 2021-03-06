<?php

namespace App\Services\Users;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Exceptions\MyApplicationHttpException;
use App\Exceptions\ExceptionStatusCodeMessages;
use App\Http\Requests\Admins\Coins\CoinCreateRequest;
use App\Http\Requests\Admins\Coins\CoinDeleteRequest;
use App\Http\Requests\Admins\Coins\CoinUpdateRequest;
use App\Http\Resources\Admins\CoinsResource;
use App\Http\Resources\Logs\UserCoinPaymentLogResource;
use App\Http\Resources\Users\UserCoinPaymentStatusResource;
use App\Http\Resources\Users\UserCoinsResource;
use App\Repositories\Admins\Coins\CoinsRepositoryInterface;
use App\Repositories\Logs\UserCoinPaymentLog\UserCoinPaymentLogRepositoryInterface;
use App\Repositories\Users\UserCoinPaymentStatus\UserCoinPaymentStatusRepositoryInterface;
use App\Repositories\Users\UserCoins\UserCoinsRepositoryInterface;
use App\Library\Array\ArrayLibrary;
use App\Library\Cache\CacheLibrary;
use App\Library\Stripe\CheckoutLibrary;
use App\Library\String\UuidLibrary;
use App\Models\Masters\Coins;
use App\Models\Users\UserCoinPaymentStatus;
use App\Models\Users\UserCoins;
use App\Repositories\Users\UserCoins\UserCoinsRepository;
use Exception;

class UserCoinPaymentService
{
    // cache keys
    private const CACHE_KEY_USER_COIN_COLLECTION_LIST = 'user_coin_collection_list';

    protected CoinsRepositoryInterface $coinsRepository;
    protected UserCoinPaymentStatusRepositoryInterface $userCoinPaymentStatusRepository;
    protected UserCoinsRepositoryInterface $userCoinsRepository;
    protected UserCoinPaymentLogRepositoryInterface $userCoinPaymentLogRepository;

    /**
     * create instance
     *
     * @param CoinsRepositoryInterface $coinsRepository
     * @param UserCoinPaymentStatusRepositoryInterface $userCoinPaymentStatusRepository
     * @param UserCoinsRepositoryInterface $userCoinsRepository
     * @param UserCoinPaymentLogRepositoryInterface $userCoinPaymentLogRepository
     * @return void
     */
    public function __construct(
        CoinsRepositoryInterface $coinsRepository,
        UserCoinPaymentStatusRepositoryInterface $userCoinPaymentStatusRepository,
        UserCoinsRepositoryInterface $userCoinsRepository,
        UserCoinPaymentLogRepositoryInterface $userCoinPaymentLogRepository
    ) {
        $this->coinsRepository = $coinsRepository;
        $this->userCoinPaymentStatusRepository = $userCoinPaymentStatusRepository;
        $this->userCoinsRepository = $userCoinsRepository;
        $this->userCoinPaymentLogRepository = $userCoinPaymentLogRepository;
    }

    /**
     * get stripe chceckout session.
     *
     * @param int $userId user id
     * @param int $coinId coin id
     * @return JsonResponse
     */
    public function getCheckout(int $userId, int $coinId): JsonResponse
    {
        $coin = $this->getCoinByCoinId($coinId);

        $item = [
            CheckoutLibrary::REQUEST_KEY_LINE_ITEM_NAME => $coin[Coins::NAME],
            CheckoutLibrary::REQUEST_KEY_LINE_ITEM_DESCRIPTION => $coin[Coins::DETAIL],
            CheckoutLibrary::REQUEST_KEY_LINE_ITEM_AMOUNT => $coin[Coins::COST],
            CheckoutLibrary::REQUEST_KEY_LINE_ITEM_CURRENCY => CheckoutLibrary::CURRENCY_TYPE_JPY,
            CheckoutLibrary::REQUEST_KEY_LINE_ITEM_QUANTITY => 1,
        ];

        $lineItems = [$item];

        $orderId = UuidLibrary::uuidVersion4();

        // stripe??????API???????????????&????????????????????????
        $session = CheckoutLibrary::createSession($orderId, $lineItems);

        // DB ??????
        DB::beginTransaction();
        try {
            // ????????????????????????
            $status = $this->getPaymentStatusFromStripeResponse($session->status);
            $stateResource = UserCoinPaymentStatusResource::toArrayForCreate($userId, $orderId, $coinId, $status, $session->id);
            $this->userCoinPaymentStatusRepository->createUserCoinPaymentStatus($userId, $stateResource);

            // ???????????????
            $userCoinPaymentLogResource = UserCoinPaymentLogResource::toArrayForCreate($userId, $orderId, $coinId, $status);
            $this->userCoinPaymentLogRepository->createUserCoinPaymentLog($userId, $userCoinPaymentLogResource);

            DB::commit();
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'message: ' . json_encode($e->getMessage()));
            DB::rollback();
            throw $e;
        }

        return response()->json(
            [
                'code' => 200,
                'message' => 'Successfully Create Session: ' . $status,
                'data' => $session->toArray(),
            ]
        );
    }

    /**
     * cancel stripe chceckout session.
     *
     * @param int $userId user id
     * @param string $orderId order id.
     * @return JsonResponse
     */
    public function cancelCheckout(int $userId, string $orderId): JsonResponse
    {
        // ????????????????????????????????????
        $userCoinPaymentStatus = $this->getUserCoinPaymentStatusByUserId($userId, $orderId);

        // TODO ???????????????
        $session = CheckoutLibrary::cancelSession($userCoinPaymentStatus[UserCoinPaymentStatus::PAYMENT_SERVICE_ID]);

        // DB ??????
        DB::beginTransaction();
        try {
            // ????????????????????????(???????????????)
            $stateResource = UserCoinPaymentStatusResource::toArrayForUpdate(
                $userId,
                $orderId,
                $userCoinPaymentStatus[UserCoinPaymentStatus::COIN_ID],
                UserCoinPaymentStatus::PAYMENT_STATUS_CANCEL,
                $session->id
            );
            $this->userCoinPaymentStatusRepository->updateUserCoinPaymentStatus($userId, $orderId, $stateResource);

            // ???????????????
            $userCoinPaymentLogResource = UserCoinPaymentLogResource::toArrayForCreate(
                $userId,
                $orderId,
                $userCoinPaymentStatus[UserCoinPaymentStatus::COIN_ID],
                UserCoinPaymentStatus::PAYMENT_STATUS_CANCEL
            );
            $this->userCoinPaymentLogRepository->createUserCoinPaymentLog($userId, $userCoinPaymentLogResource);

            DB::commit();
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'message: ' . json_encode($e->getMessage()));
            DB::rollback();
            throw $e;
        }

        return response()->json(
            [
                'code' => 200,
                'message' => 'Successfully Cancel Create Session. ' . $orderId,
                'data' => $session->toArray(),
            ]
        );
    }

    /**
     * complete stripe chceckout session.
     *
     * @param int $userId user id
     * @param string $orderId order id.
     * @return JsonResponse
     */
    public function completeCheckout(int $userId, string $orderId): JsonResponse
    {
        // ????????????????????????????????????
        $userCoinPaymentStatus = $this->getUserCoinPaymentStatusByUserId($userId, $orderId);

        // TODO ???????????????
        $session = CheckoutLibrary::completeSession($userCoinPaymentStatus[UserCoinPaymentStatus::PAYMENT_SERVICE_ID]);

        // DB ??????
        DB::beginTransaction();
        try {
            // ????????????????????????(??????)
            $stateResource = UserCoinPaymentStatusResource::toArrayForUpdate(
                $userId,
                $orderId,
                $userCoinPaymentStatus[UserCoinPaymentStatus::COIN_ID],
                UserCoinPaymentStatus::PAYMENT_STATUS_COMPLETE,
                $session->id
            );
            $this->userCoinPaymentStatusRepository->updateUserCoinPaymentStatus($userId, $orderId, $stateResource);

            // ????????????????????????
            $coin = $this->getCoinByCoinId($userCoinPaymentStatus[UserCoinPaymentStatus::COIN_ID]);

            // ???????????????????????????????????????????????????
            $userCoin = $this->getUserCoinByUserId($userId);

            if (is_null($userCoin)) {
                // ?????????????????????????????????????????????
                $userCoinResource = UserCoinsResource::toArrayForCreate(
                    $userId,
                    UserCoins::DEFAULT_COIN_COUNT,
                    $coin[Coins::PRICE],
                    UserCoins::DEFAULT_COIN_COUNT
                );
                $this->userCoinsRepository->createUserCoins($userId, $userCoinResource);
            } else {
                // ???????????????????????????????????????
                $userCoinResource = UserCoinsResource::toArrayForUpdate(
                    $userId,
                    $userCoin[UserCoins::FREE_COINS],
                    $userCoin[UserCoins::PAID_COINS] + $coin[Coins::PRICE],
                    $userCoin[UserCoins::LIMITED_TIME_COINS]
                );
                $this->userCoinsRepository->updateUserCoins($userId, $userCoinResource);
            }

            // ???????????????
            $userCoinPaymentLogResource = UserCoinPaymentLogResource::toArrayForCreate(
                $userId,
                $orderId,
                $userCoinPaymentStatus[UserCoinPaymentStatus::COIN_ID],
                UserCoinPaymentStatus::PAYMENT_STATUS_COMPLETE
            );
            $this->userCoinPaymentLogRepository->createUserCoinPaymentLog($userId, $userCoinPaymentLogResource);

            DB::commit();
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'message: ' . json_encode($e->getMessage()));
            DB::rollback();
            throw $e;
        }

        return response()->json(
            [
                'code' => 200,
                'message' => 'Successfully Payment! ' . $orderId,
                'data' => $session->toArray(),
            ]
        );
    }

    /**
     * get coins data.
     *
     * @return array
     */
    private function getCoins(): array
    {
        $cache = CacheLibrary::getByKey(self::CACHE_KEY_USER_COIN_COLLECTION_LIST);

        // ???????????????????????????
        if (is_null($cache)) {
            $collection = $this->coinsRepository->getCoins();
            $resourceCollection = CoinsResource::toArrayForGetCoinsCollection($collection);

            if (!empty($resourceCollection)) {
                CacheLibrary::setCache(self::CACHE_KEY_USER_COIN_COLLECTION_LIST, $resourceCollection);
            }
        } else {
            $resourceCollection = $cache;
        }

        return $resourceCollection;
    }

    /**
     * get coins by coin id.
     *
     * @param int $coinId coin id
     * @return array
     */
    private function getCoinByCoinId(int $coinId): array
    {
        $coins = $this->coinsRepository->getById($coinId);

        if (is_null($coins)) {
            // $validator->errors()->toArray();
            throw new MyApplicationHttpException(
                ExceptionStatusCodeMessages::STATUS_CODE_404,
                'not exitst coin.'
            );
        }

        // ?????????????????????repository??????????????????
        // $coin = json_decode(json_encode($coins->toArray()[0]), true);
        $coin = ArrayLibrary::toArray($coins->toArray()[0]);
        return $coin;
    }

    /**
     * get user coins by user id.
     *
     * @param int $userId user id
     * @return array|null
     */
    private function getUserCoinByUserId(int $userId): array|null
    {
        $userCoin = $this->userCoinsRepository->getByUserId($userId);

        if (is_null($userCoin)) {
            return $userCoin;
        }

        // ?????????????????????repository??????????????????
        return ArrayLibrary::toArray($userCoin->toArray()[0]);
    }

    /**
     * get user coin payment status by user id.
     *
     * @param int $userId user id
     * @param string $orderId order id
     * @return array
     */
    private function getUserCoinPaymentStatusByUserId(int $userId, string $orderId): array
    {
        $userCoinPaymentStatus = $this->userCoinPaymentStatusRepository->getByUserIdAndOrderId($userId, $orderId);

        if (empty($userCoinPaymentStatus)) {
            throw new MyApplicationHttpException(
                ExceptionStatusCodeMessages::STATUS_CODE_500,
                'not exist userCoinPaymentStatus.'
            );
        }

        // ?????????????????????repository??????????????????
        return ArrayLibrary::toArray($userCoinPaymentStatus->toArray()[0]);
    }

    /**
     * get payment status value from stripe session status.
     *
     * @param string $status checkout session status
     * @return int
     */
    private function getPaymentStatusFromStripeResponse(string $status): int
    {
        if (empty(CheckoutLibrary::CHECKOUT_STATUS_VALUE_LIST[$status])) {
            // $validator->errors()->toArray();
            throw new MyApplicationHttpException(
                ExceptionStatusCodeMessages::STATUS_CODE_500,
                'invalide status value.'
            );
        }

        return CheckoutLibrary::CHECKOUT_STATUS_VALUE_LIST[$status];
    }
}
