<?php

namespace App\Library\Cache;

use Illuminate\Redis\RedisManager;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redis;
use Predis\Response\Status;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Cache\CacheLibrary;
use App\Library\Message\StatusCodeMessages;
use App\Library\Time\TimeLibrary;
use App\Trait\CheckHeaderTrait;

class LogicCacheLibrary extends CacheLibrary
{
    use CheckHeaderTrait;

    // database.phpのキー名
    protected const REDIS_CONNECTION = 'cache';

    private const CACHE_KEY_CONTACT_BODY = 'contact_body';

    /**
     * get contact body cache Key.
     *
     * @param string $body contact body.
     * @return string
     */
    public static function getContactDetailKey(string $body): string
    {
        $hash = md5($body);
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
     * set contact body cache.
     *
     * @param string $value
     * @return void
     */
    public static function getContactCache(string $value): void
    {
        self::getByKey(self::getContactDetailKey($value));
    }
}
