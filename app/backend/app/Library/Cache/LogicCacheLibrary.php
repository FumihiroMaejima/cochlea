<?php

namespace App\Library\Cache;

use App\Library\Cache\CacheLibrary;
use App\Library\Hash\HashLibrary;
use App\Library\Time\TimeLibrary;

class LogicCacheLibrary extends CacheLibrary
{
    // database.phpのキー名
    protected const REDIS_CONNECTION = 'cache';

    // キャッシュキー
    private const CACHE_KEY_CONTACT_BODY = 'contact_body';

    /**
     * get contact body cache Key.
     *
     * @param string $body contact body.
     * @return string
     */
    public static function getContactDetailKey(string $body): string
    {
        $hash = HashLibrary::hash($body, HashLibrary::ALGORITHM_MD5);
        // return self::CACHE_KEY_CONTACT_BODY . '_' . TimeLibrary::getCurrentDateTime(TimeLibrary::DATE_TIME_FORMAT_YMD);
        return self::CACHE_KEY_CONTACT_BODY . '_' . $hash;
    }

    /**
     * set contact body cache.
     *
     * @param string $value
     * @return void
     */
    public static function setContactCache(string $value): void
    {
        self::setCache(self::getContactDetailKey($value), $value);
    }

    /**
     * get contact body cache.
     *
     * @param string $value
     * @return ?string
     */
    public static function getContactCache(string $value): ?string
    {
        return self::getByKey(self::getContactDetailKey($value));
    }
}
