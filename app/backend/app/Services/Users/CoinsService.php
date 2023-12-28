<?php

declare(strict_types=1);

namespace App\Services\Users;

use Illuminate\Support\Facades\Config;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Http\Resources\Users\CoinsResource;
use App\Repositories\Masters\Coins\CoinsRepositoryInterface;
use App\Library\Array\ArrayLibrary;
use App\Library\Cache\CacheLibrary;
// use App\Library\Cache\HashCacheLibrary;
use Exception;

class CoinsService
{
    // cache keys
    private const CACHE_KEY_USER_COINS_LIST = 'cache_user_coins_list';

    protected CoinsRepositoryInterface $coinsRepository;

    /**
     * create CoinsService instance
     *
     * @param  \App\Repositories\Masters\Coins\CoinsRepositoryInterface $coinsRepository
     * @return void
     */
    public function __construct(CoinsRepositoryInterface $coinsRepository)
    {
        $this->coinsRepository = $coinsRepository;
    }

    /**
     * get coins data
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function getCoins(): JsonResponse
    {
        $cache = CacheLibrary::getByKey(self::CACHE_KEY_USER_COINS_LIST);
        // hash型の検証
        // $testCache = HashCacheLibrary::getByKey(self::CACHE_KEY_USER_COINS_LIST.'_test');

        // キャッシュチェック
        if (is_null($cache)) {
            $collection = $this->coinsRepository->getRecords();
            $resourceCollection = CoinsResource::toArrayForGetTextAndValueList($collection);

            if (!empty($resourceCollection)) {
                CacheLibrary::setCache(self::CACHE_KEY_USER_COINS_LIST, $resourceCollection);
                // HashCacheLibrary::setCache(self::CACHE_KEY_USER_COINS_LIST.'_test', $resourceCollection);
            }
        } else {
            $resourceCollection = $cache;
        }

        return response()->json(['data' => $resourceCollection], StatusCodeMessages::STATUS_200);
    }

    /**
     * get coin by coin id.
     *
     * @param int $coinId coin id
     * @return array
     */
    private function getCoinById(int $coinId): array
    {
        // 更新用途で使う為lockをかける
        $coins = $this->coinsRepository->getById($coinId, true);

        if (empty($coins)) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_500,
                'not exist coin.'
            );
        }

        // 複数チェックはrepository側で実施済み
        return ArrayLibrary::toArray(ArrayLibrary::getFirst($coins->toArray()));
    }

    /**
     * get coins by coin ids.
     *
     * @param array $coinIds coin id
     * @return array
     */
    private function getCoinsByIds(array $coinIds): array
    {
        // 更新用途で使う為lockをかける
        $roles = $this->coinsRepository->getByIds($coinIds, true);

        if (empty($roles)) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_500,
                'not exist roles.'
            );
        }

        // 複数チェックはrepository側で実施済み
        return ArrayLibrary::toArray($roles->toArray());
    }
}
