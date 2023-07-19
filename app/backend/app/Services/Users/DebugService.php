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
use App\Library\Message\StatusCodeMessages;
use App\Http\Resources\Users\UserCoinHistoriesResource;
use App\Http\Resources\Users\UserCoinsResource;
use App\Library\Array\ArrayLibrary;
use App\Library\Cache\LogicCacheLibrary;
use App\Library\Cache\MasterCacheLibrary;
use App\Library\Stripe\CheckoutLibrary;
use App\Library\File\PdfLibrary;
use App\Library\Random\RandomLibrary;
use App\Library\String\SurrogatePair;
use App\Library\String\UuidLibrary;
use App\Library\Time\TimeLibrary;
use App\Repositories\Masters\Coins\CoinsRepositoryInterface;
use App\Repositories\Logs\UserCoinPaymentLog\UserCoinPaymentLogRepositoryInterface;
use App\Repositories\Users\UserCoinHistories\UserCoinHistoriesRepositoryInterface;
use App\Repositories\Users\UserCoinPaymentStatus\UserCoinPaymentStatusRepositoryInterface;
use App\Repositories\Users\UserCoins\UserCoinsRepositoryInterface;
use App\Models\Masters\OAuthUsers;
use App\Models\User;
use App\Models\Users\UserCoinHistories;
use App\Models\Users\UserCoins;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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
    ) {
        $this->prop = 'debug propaty';
        // コイン関係
        $this->coinsRepository = $coinsRepository;
        $this->userCoinHistoriesRepositoryInterface = $userCoinHistoriesRepositoryInterface;
        $this->userCoinPaymentStatusRepository = $userCoinPaymentStatusRepository;
        $this->userCoinsRepository = $userCoinsRepository;
        $this->userCoinPaymentLogRepository = $userCoinPaymentLogRepository;
    }

    /**
     * デバッグ関連情報取得と整形
     *
     * @param int $userId
     * @param ?string $sessionId
     * @param ?int $fakerTimeStamp
     * @param ?string $clinetIp
     * @param ?string $userAgent
     * @return JsonResponse
     * @throws MyApplicationHttpException
     */
    public function getDebugStatus(
        int $userId,
        ?string $sessionId,
        ?int $fakerTimeStamp,
        ?string $clinetIp,
        ?string $userAgent
    ): JsonResponse {
        $user = $userId > 0 ? (new User())->getRecordByUserId($userId) : null;
        $oAuthUser = (new OAuthUsers())->getRecordByUserId($userId);

        $response = [
            'userId' => $userId,
            'sessionId' => $sessionId,
            'email' => $user[User::EMAIL] ?? null,
            'name' => $user[User::NAME] ?? null,
            'createdAt' => $user[User::CREATED_AT] ?? null,
            'codeVerifiedAt' => $user[User::CODE_VERIFIED_AT] ?? null,
            'lastLoginAt' => $user[User::LAST_LOGIN_AT] ?? null,
            'fakerTimeStamp' => $fakerTimeStamp,
            'host' => config('app.url'),
            'clinetIp' => $clinetIp,
            'userAgent' => $userAgent,
            OAuthUsers::GIT_HUB_ID => $oAuthUser[OAuthUsers::GIT_HUB_ID] ?? null,
            OAuthUsers::TWITTER_ID => $oAuthUser[OAuthUsers::TWITTER_ID] ?? null,
            OAuthUsers::FACEBOOK_ID => $oAuthUser[OAuthUsers::FACEBOOK_ID] ?? null,
        ];

        return response()->json(['data' => $response, 'status' => 200]);
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
    ): JsonResponse {
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
                UuidLibrary::uuidVersion4(),
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
     * check value is emoji.
     *
     * @param string $value
     * @return JsonResponse
     * @throws Exception
     */
    public function checkIsEmoji(string $value): JsonResponse
    {
        $result = SurrogatePair::isNotSurrogatePair($value);

        $dec = SurrogatePair::getUnicodeFromEmoji($value);
        $hex = SurrogatePair::getUnicodeFromEmoji($value, true);

        // 16進数
        $hex = SurrogatePair::getUnicodeFromEmoji($value, true);
        $hexLen = SurrogatePair::getUnicodeLength($hex, true);

        // 10進数
        $dec = SurrogatePair::getUnicodeFromEmoji($value);
        $decLen = SurrogatePair::getUnicodeLength($dec);

        return response()->json(
            [
                'code' => 200,
                'message' => 'Success.',
                'data' => [
                    'isSurrogatePair' => !$result,
                    'values' => [
                        'hex' => [
                            'value' => $hex,
                            'length' => $hexLen,
                            'format' => SurrogatePair::formatUnicode($hex, true),
                        ],
                        'decimal' => [
                            'value' => $dec,
                            'length' => $decLen,
                            'format' => SurrogatePair::formatUnicode($dec),
                        ],
                    ],
                ],
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

    /**
     * get user coin history pdf by uuid.
     *
     * @param int $userId user id
     * @param string $uuid uuid
     * @return BinaryFileResponse
     */
    public function getCoinHistoryPdfByUuid(int $userId, string $uuid): BinaryFileResponse
    {
        $coinHistory = $this->userCoinHistoriesRepositoryInterface->getByUserIdAndUuId($userId, $uuid);

        if (is_null($coinHistory)) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_404,
                'not exitst coin history.'
            );
        }

        $coinHistory = ArrayLibrary::getFirst(ArrayLibrary::toArray($coinHistory->toArray()));

        $updatedAt = $coinHistory[UserCoinHistories::UPDATED_AT];
        $type = UserCoinHistories::USER_COINS_HISTORY_TYPE_VALUE_LIST[$coinHistory[UserCoinHistories::TYPE]];
        $expireCoin = $coinHistory[UserCoinHistories::GET_LIMITED_TIME_COINS];
        $freeCoin = $coinHistory[UserCoinHistories::GET_FREE_COINS];
        $paidCoin = $coinHistory[UserCoinHistories::GET_PAID_COINS];
        $fileName = 'コイン履歴_' . TimeLibrary::strToTimeStamp($updatedAt) . '.pdf';

        $html = <<< EOF
        <style>
        body {
            color: #212121;
        }
        </style>
        <body>
        <h1>コイン履歴 $updatedAt</h1>
        <p>
        種類: $type
        </p>
        <p>
        期限付きコイン: $expireCoin
        </p>
        <p>
        無料コイン: $freeCoin
        </p>
        <p>
        有料コイン: $paidCoin
        </p>
        </body>
        EOF;

        return response()->file(PdfLibrary::getPdfByHtmlString($fileName, $html));
    }

    /**
     * remove server cache.
     *
     * @param ?string $type cache connection.
     * @return bool
     */
    public function removeCacheServerCache(?string $type = 'all'): bool
    {
        switch ($type) {
            case MasterCacheLibrary::getConnection():
                MasterCacheLibrary::removeAllKeys();
                break;
            case LogicCacheLibrary::getConnection():
                LogicCacheLibrary::removeAllKeys();
                break;
            default:
                MasterCacheLibrary::removeAllKeys();
                LogicCacheLibrary::removeAllKeys();
                break;
        }
        return true;
    }
}
