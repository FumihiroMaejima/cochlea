<?php

namespace App\Services\Users;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use App\Exceptions\MyApplicationHttpException;
use App\Http\Resources\Users\UserCoinHistoriesResource;
use App\Http\Resources\Users\UserCoinsResource;
use App\Library\Array\ArrayLibrary;
use App\Library\Stripe\CheckoutLibrary;
use App\Library\Random\RandomLibrary;
use App\Library\String\UuidLibrary;
use App\Library\Time\TimeLibrary;
use App\Repositories\Admins\Coins\CoinsRepositoryInterface;
use App\Repositories\Logs\UserCoinPaymentLog\UserCoinPaymentLogRepositoryInterface;
use App\Repositories\Users\UserCoinHistories\UserCoinHistoriesRepositoryInterface;
use App\Repositories\Users\UserCoinPaymentStatus\UserCoinPaymentStatusRepositoryInterface;
use App\Repositories\Users\UserCoins\UserCoinsRepositoryInterface;
use App\Models\Users\UserCoinHistories;
use App\Models\Users\UserCoins;
use Exception;

class DebugService
{
    protected string $prop;


    protected CoinsRepositoryInterface $coinsRepository;
    protected UserCoinHistoriesRepositoryInterface $userCoinHistoriesRepositoryInterface;
    protected UserCoinPaymentStatusRepositoryInterface $userCoinPaymentStatusRepository;
    protected UserCoinsRepositoryInterface $userCoinsRepository;
    protected UserCoinPaymentLogRepositoryInterface $userCoinPaymentLogRepository;

    /**
     * create DebugService instance
     *
     * @param CoinsRepositoryInterface $coinsRepository
     * @param UserCoinHistoriesRepositoryInterface $userCoinHistoriesRepositoryInterface
     * @param UserCoinPaymentStatusRepositoryInterface $userCoinPaymentStatusRepository
     * @param UserCoinsRepositoryInterface $userCoinsRepository
     * @param UserCoinPaymentLogRepositoryInterface $userCoinPaymentLogRepository
     * @return void
     */
    public function __construct(
        CoinsRepositoryInterface $coinsRepository,
        UserCoinHistoriesRepositoryInterface $userCoinHistoriesRepositoryInterface,
        UserCoinPaymentStatusRepositoryInterface $userCoinPaymentStatusRepository,
        UserCoinsRepositoryInterface $userCoinsRepository,
        UserCoinPaymentLogRepositoryInterface $userCoinPaymentLogRepository
    )
    {
        $this->prop = 'debug propaty';
        // コイン関係
        $this->coinsRepository = $coinsRepository;
        $this->userCoinHistoriesRepositoryInterface = $userCoinHistoriesRepositoryInterface;
        $this->userCoinPaymentStatusRepository = $userCoinPaymentStatusRepository;
        $this->userCoinsRepository = $userCoinsRepository;
        $this->userCoinPaymentLogRepository = $userCoinPaymentLogRepository;
    }

    /**
     * get stripe chceckout session.
     *
     * @param  \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function getCheckout(): JsonResponse
    {
        $lineItems = [
            [
              CheckoutLibrary::REQUEST_KEY_LINE_ITEM_NAME => 'test product',
              CheckoutLibrary::REQUEST_KEY_LINE_ITEM_DESCRIPTION => 'test description',
              CheckoutLibrary::REQUEST_KEY_LINE_ITEM_AMOUNT => 600,
              CheckoutLibrary::REQUEST_KEY_LINE_ITEM_CURRENCY => CheckoutLibrary::CURRENCY_TYPE_JPY,
              CheckoutLibrary::REQUEST_KEY_LINE_ITEM_QUANTITY => 2,
            ],
        ];

        $session = CheckoutLibrary::debugCreateSession(UuidLibrary::uuidVersion4(), $lineItems);

        return response()->json(
            [
                'code' => 200,
                'message' => 'Successfully Create Session',
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
     * assign coins.
     *
     * @param int $userId user id
     * @param int $freeCoins free coin count
     * @param int $paidCoins paid coin count
     * @param int $limitedTimeCoins limited time coin count
     * @param string|null $expiredAt limited time coin exprired time
     * @return JsonResponse
     */
    public function assignCoins(
        int $userId,
        int $freeCoins,
        int $paidCoins,
        int $limitedTimeCoins,
        string|null $expiredAt = null
    ): JsonResponse
    {

        // DB 登録
        DB::beginTransaction();
        try {
            // ユーザーの所持しているコインの更新
            $userCoin = $this->getUserCoinByUserId($userId);

            if (is_null($userCoin)) {
                // 登録されていない場合は新規登録
                $userCoinResource = UserCoinsResource::toArrayForCreate(
                    $userId,
                    $freeCoins,
                    $paidCoins,
                    $limitedTimeCoins
                );
                $this->userCoinsRepository->create($userId, $userCoinResource);
            } else {
                // ロックをかけて再取得
                $userCoin = $this->getUserCoinByUserId($userId, true);

                // ユーザーのコイン情報の更新
                $userCoinResource = UserCoinsResource::toArrayForUpdate(
                    $userId,
                    $userCoin[UserCoins::FREE_COINS] + $freeCoins,
                    $userCoin[UserCoins::PAID_COINS] + $paidCoins,
                    $userCoin[UserCoins::LIMITED_TIME_COINS] + $limitedTimeCoins
                );
                $this->userCoinsRepository->update($userId, $userCoinResource);
            }


            // コイン履歴の設定(補填)
            $userCoinHistoriesResource = UserCoinHistoriesResource::toArrayForCreate(
                $userId,
                UserCoinHistories::USER_COINS_HISTORY_TYPE_COMPENSATION,
                $freeCoins,
                $paidCoins,
                $limitedTimeCoins,
                exipiredAt: $expiredAt
            );
            $this->userCoinHistoriesRepositoryInterface->create($userId, $userCoinHistoriesResource);

            DB::commit();
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'message: ' . json_encode($e->getMessage()));
            DB::rollback();
            throw $e;
        }

        return response()->json(
            [
                'code' => 200,
                'message' => 'Successfully Assign Coins!',
                'data' => true,
            ]
        );
    }

    /**
     * get random wighted value.
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function gacha(): JsonResponse
    {
        $entries = [10, 20, 30];
        // 対象の要素のkeyを取得
        $targetKey = RandomLibrary::getWeightedRandomValue($entries);

        return response()->json(
            [
                'code' => 200,
                'message' => 'Success.',
                'data' => $entries[$targetKey] ?? 0,
            ]
        );
    }

    /**
     * get user coins by user id.
     *
     * @param int $userId user id
     * @param bool $isLock exec lock For Update
     * @return array|null
     */
    private function getUserCoinByUserId(int $userId, bool $isLock = false): array|null
    {
        $userCoin = $this->userCoinsRepository->getByUserId($userId, $isLock);

        if (is_null($userCoin)) {
            return $userCoin;
        }

        // 複数チェックはrepository側で実施済み
        return ArrayLibrary::toArray(ArrayLibrary::getFirst($userCoin->toArray()));
    }
}
