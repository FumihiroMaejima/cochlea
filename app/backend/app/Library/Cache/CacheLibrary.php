<?php

namespace App\Library\Cache;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redis;
use Predis\Response\Status;
use App\Exceptions\ExceptionStatusCodeMessages;
use App\Exceptions\MyApplicationHttpException;
use App\Trait\CheckHeaderTrait;

class CacheLibrary
{
    use CheckHeaderTrait;

    private const SET_CACHE_RESULT_VALUE = 'OK';

    private const DELETE_CACHE_RESULT_VALUE_SUCCESS = 1;
    private const DELETE_CACHE_RESULT_VALUE_NO_DATA = 0;

    /**
     * get cache value by Key.
     *
     * @param string $key
     * @return mixed
     */
    public static function getByKey(string $key): mixed
    {
        $cache = Redis::get($key);

        return is_null($cache) ? $cache : json_decode($cache, true);
    }

    /**
     * set cache to redis.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function setCache(string $key, mixed $value): void
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }

        /** @var Status $result redisへの設定処理結果 */
        $result = Redis::set($key, $value);
        $payload = $result->getPayload();

        if ($payload !== self::SET_CACHE_RESULT_VALUE) {
            throw new MyApplicationHttpException(
                ExceptionStatusCodeMessages::STATUS_CODE_500,
                'set cache action is failure.'
            );
        }
    }

    /**
     * remove cache by request header data.
     *
     * @param string $key
     * @param bool $isIgnore ignore data check.
     * @return bool
     */
    public static function deleteCache(string $key, bool $isIgnore = false): void
    {
        $cache = self::getByKey($key);

        if (empty($cache)) {
            if ($isIgnore) {
                return;
            }

            throw new MyApplicationHttpException(
                ExceptionStatusCodeMessages::STATUS_CODE_500,
                'cache is not exist.'
            );
        }

        /** @var int $result 削除結果 */
        $result = Redis::del($key);

        if (($result !== self::DELETE_CACHE_RESULT_VALUE_SUCCESS) && !$isIgnore) {
            throw new MyApplicationHttpException(
                ExceptionStatusCodeMessages::STATUS_CODE_500,
                'delete cache action is failure.'
            );
        }
    }

    /**
     * check has cache by key.
     *
     * @param string $key
     * @return bool
     */
    public static function hasCache(string $key): bool
    {
        $cache = Redis::get($key);

        return $cache ? true : false;
    }
}
