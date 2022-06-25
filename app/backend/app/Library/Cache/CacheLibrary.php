<?php

namespace App\Library\Cache;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redis;
use App\Exceptions\ExceptionStatusCodeMessages;
use App\Exceptions\MyApplicationHttpException;
use App\Trait\CheckHeaderTrait;

class CacheLibrary
{
    use CheckHeaderTrait;

    /**
     * get cache value by Key.
     *
     * @param string $key
     * @return string
     */
    public static function getByKey(string $key): string
    {
        $session = Redis::get($key);

        return $session ?? '';
    }

    /**
     * set cache to redis.
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public static function setCache(string $key, mixed $value): void
    {
        Redis::set($key, $value);
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
                ExceptionStatusCodeMessages::MESSAGE_500,
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
