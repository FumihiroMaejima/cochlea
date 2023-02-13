<?php

namespace App\Services\Users;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Http\Requests\Admin\Coins\CoinCreateRequest;
use App\Http\Requests\Admin\Coins\CoinDeleteRequest;
use App\Http\Requests\Admin\Coins\CoinUpdateRequest;
use App\Http\Resources\Admins\CoinsResource;
use App\Http\Resources\Logs\UserCoinPaymentLogResource;
use App\Http\Resources\Users\UserCoinHistoriesResource;
use App\Http\Resources\Users\UserCoinPaymentStatusResource;
use App\Http\Resources\Users\UserCoinsResource;
use App\Repositories\Admins\Coins\CoinsRepositoryInterface;
use App\Repositories\Logs\UserCoinPaymentLog\UserCoinPaymentLogRepositoryInterface;
use App\Repositories\Users\UserCoinHistories\UserCoinHistoriesRepositoryInterface;
use App\Repositories\Users\UserCoinPaymentStatus\UserCoinPaymentStatusRepositoryInterface;
use App\Repositories\Users\UserCoins\UserCoinsRepositoryInterface;
use App\Library\Array\ArrayLibrary;
use App\Library\Cache\CacheLibrary;
use App\Library\Stripe\CheckoutLibrary;
use App\Library\String\UuidLibrary;
use App\Models\Masters\Coins;
use App\Models\Users\UserCoinHistories;
use App\Models\Users\UserCoinPaymentStatus;
use App\Models\Users\UserCoins;
use App\Repositories\Users\UserCoins\UserCoinsRepository;
use Exception;

class UserCoinHistoryService
{
    // cache keys
    private const CACHE_KEY_USER_COIN_COLLECTION_LIST = 'user_coin_collection_list';

    protected CoinsRepositoryInterface $coinsRepository;
    protected UserCoinHistoriesRepositoryInterface $userCoinHistoriesRepositoryInterface;
    protected UserCoinPaymentStatusRepositoryInterface $userCoinPaymentStatusRepository;
    protected UserCoinsRepositoryInterface $userCoinsRepository;
    protected UserCoinPaymentLogRepositoryInterface $userCoinPaymentLogRepository;

    /**
     * create instance
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
        $this->coinsRepository = $coinsRepository;
        $this->userCoinHistoriesRepositoryInterface = $userCoinHistoriesRepositoryInterface;
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
    public function getCoinHistory(int $userId): JsonResponse
    {
        $coin = $this->userCoinHistoriesRepositoryInterface->getListByUserId($userId);

        return response()->json(
            [
                'code' => 200,
                'message' => 'Success',
                'data' => $coin->toArray(),
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
            $collection = $this->coinsRepository->getRecords();
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
                StatusCodeMessages::STATUS_404,
                'not exitst coin.'
            );
        }

        // 複数チェックはrepository側で実施済み
        // $coin = json_decode(json_encode($coins->toArray()[0]), true);
        return ArrayLibrary::toArray(ArrayLibrary::getFirst($coins->toArray()));
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
