<?php

namespace App\Services\Users;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\Admins\Coins\CoinCreateRequest;
use App\Http\Requests\Admins\Coins\CoinDeleteRequest;
use App\Http\Requests\Admins\Coins\CoinUpdateRequest;
use App\Http\Resources\Admins\CoinsResource;
use App\Repositories\Admins\Coins\CoinsRepositoryInterface;
use App\Repositories\Users\UserCoinPaymentStatus\UserCoinPaymentStatusRepositoryInterface;
use App\Library\Cache\CacheLibrary;
use App\Library\Stripe\CheckoutLibrary;
use App\Library\String\UuidLibrary;
use Exception;

class UserCoinPaymentService
{
    // cache keys
    private const CACHE_KEY_USER_COIN_COLLECTION_LIST = 'user_coin_collection_list';

    protected CoinsRepositoryInterface $coinsRepository;
    protected UserCoinPaymentStatusRepositoryInterface $userCoinPaymentStatusRepository;

    /**
     * create instance
     *
     * @param CoinsRepositoryInterface $coinsRepository
     * @param UserCoinPaymentStatusRepositoryInterface $userCoinPaymentStatusRepository
     * @return void
     */
    public function __construct(
        CoinsRepositoryInterface $coinsRepository,
        UserCoinPaymentStatusRepositoryInterface $userCoinPaymentStatusRepository
    )
    {
        $this->coinsRepository = $coinsRepository;
        $this->userCoinPaymentStatusRepository = $userCoinPaymentStatusRepository;
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

        $session = CheckoutLibrary::createSession($lineItems, UuidLibrary::uuidVersion4());

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
     * get coins data
     *
     * @return array
     */
    public function getCoins(): array
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
}
