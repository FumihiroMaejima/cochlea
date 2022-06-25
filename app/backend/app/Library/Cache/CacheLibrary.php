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

        /**
         * @var Status $result;
         */
        $result = Redis::set($key, $value);
        $payload = $result->getPayload();

        if ($payload !== self::SET_CACHE_RESULT_VALUE) {
            throw new MyApplicationHttpException(
                ExceptionStatusCodeMessages::STATUS_CODE_500,
                'cache set action is failure.'
            );
        }
    }

    /**
     * remove session by request header data.
     *
     * @param string $key
     * @return bool
     */
    public static function removeSession(string $key): void
    {
        $cache = self::getByKey($key);

        if (empty($cache)) {
            throw new MyApplicationHttpException(
                ExceptionStatusCodeMessages::STATUS_CODE_500,
                'cache is not exist.'
            );
        }

        Redis::del($key);
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
