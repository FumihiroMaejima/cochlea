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
use App\Http\Resources\Users\UserCoinPaymentStatusResource;
use App\Repositories\Admins\Coins\CoinsRepositoryInterface;
use App\Repositories\Users\UserCoinPaymentStatus\UserCoinPaymentStatusRepositoryInterface;
use App\Repositories\Users\UserCoins\UserCoinsRepositoryInterface;
use App\Library\Array\ArrayLibrary;
use App\Library\Cache\CacheLibrary;
use App\Library\Stripe\CheckoutLibrary;
use App\Library\String\UuidLibrary;
use App\Models\Masters\Coins;
use Exception;

class UserCoinPaymentService
{
    // cache keys
    private const CACHE_KEY_USER_COIN_COLLECTION_LIST = 'user_coin_collection_list';

    protected CoinsRepositoryInterface $coinsRepository;
    protected UserCoinPaymentStatusRepositoryInterface $userCoinPaymentStatusRepository;
    protected UserCoinsRepositoryInterface $userCoinsRepository;

    /**
     * create instance
     *
     * @param CoinsRepositoryInterface $coinsRepository
     * @param UserCoinPaymentStatusRepositoryInterface $userCoinPaymentStatusRepository
     * @param UserCoinsRepositoryInterface $userCoinsRepository
     * @return void
     */
    public function __construct(
        CoinsRepositoryInterface $coinsRepository,
        UserCoinPaymentStatusRepositoryInterface $userCoinPaymentStatusRepository,
        UserCoinsRepositoryInterface $userCoinsRepository
    )
    {
        $this->coinsRepository = $coinsRepository;
        $this->userCoinPaymentStatusRepository = $userCoinPaymentStatusRepository;
        $this->userCoinsRepository = $userCoinsRepository;
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
            CheckoutLibrary::REQUEST_KEY_LINE_ITEM_AMOUNT => $coin[Coins::PRICE],
            CheckoutLibrary::REQUEST_KEY_LINE_ITEM_CURRENCY => CheckoutLibrary::CURRENCY_TYPE_JPY,
            CheckoutLibrary::REQUEST_KEY_LINE_ITEM_QUANTITY => 1,
        ];

        $lineItems = [$item];

        $orderId = UuidLibrary::uuidVersion4();

        $session = CheckoutLibrary::createSession($orderId, $lineItems);

        // DB 登録
        DB::beginTransaction();
        try {
            // ステータス
            $status = $this->getPaymentStatusFromStripeResponse($session->status);
            $stateResource = UserCoinPaymentStatusResource::toArrayForCreate($userId, $orderId, $coinId, $status);
            $this->userCoinPaymentStatusRepository->createUserCoinPaymentStatus($userId, $stateResource);

            // TODO create user coin;
            // $this->userCoinsRepository->createUserCoins();

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
     * @param string $orderId order id.
     * @return JsonResponse
     */
    public function cancelCheckout(string $orderId): JsonResponse
    {
         return response()->json(
            [
                'code' => 200,
                'message' => 'Successfully Cancel Create Session. ' . $orderId,
                'data' => [],
            ]
        );
    }

    /**
     * complete stripe chceckout session.
     *
     * @param string $orderId order id.
     * @return JsonResponse
     */
    public function completeCheckout(string $orderId): JsonResponse
    {
         return response()->json(
            [
                'code' => 200,
                'message' => 'Successfully Payment! ' . $orderId,
                'data' => [],
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

        // キャッシュチェック
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

        // 複数チェックはrepository側で実施済み
        // $coin = json_decode(json_encode($coins->toArray()[0]), true);
        $coin = ArrayLibrary::toArray($coins->toArray()[0]);
        return $coin;
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
