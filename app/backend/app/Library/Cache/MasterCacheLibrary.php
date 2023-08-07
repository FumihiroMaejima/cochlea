<?php

namespace App\Library\Cache;

use App\Library\Cache\CacheLibrary;
use App\Library\Hash\HashLibrary;
use App\Library\Time\TimeLibrary;

class MasterCacheLibrary extends CacheLibrary
{
    // database.phpのキー名
    protected const REDIS_CONNECTION = 'master';

    // キャッシュキー
    private const CACHE_KEY_BANNERS_ALL = 'banners_all';
    private const CACHE_KEY_COINS_ALL = 'coins_all';
    private const CACHE_KEY_EVENTS_ALL = 'events_all';
    private const CACHE_KEY_INFORMATIONS_ALL = 'informations_all';
    private const CACHE_KEY_SERVICE_TERMS_ALL = 'service_terms_all';

    /**
     * get cache key of banners.
     *
     * @return string
     */
    public static function getBannersAllKey(): string
    {
        return self::CACHE_KEY_BANNERS_ALL . '_' . TimeLibrary::getCurrentDateTime(TimeLibrary::DATE_TIME_FORMAT_YMD);
    }

    /**
     * set cache of banners.
     *
     * @param array $value
     * @return void
     */
    public static function setBannersCache(array $value): void
    {
        self::setCache(self::getBannersAllKey(), $value);
    }

    /**
     * get cache of banners.
     *
     * @return ?string
     */
    public static function getBannersCache(): ?string
    {
        return self::getByKey(self::getBannersAllKey());
    }

    /**
     * get cache key of coins.
     *
     * @return string
     */
    public static function getCoinsAllKey(): string
    {
        return self::CACHE_KEY_COINS_ALL . '_' . TimeLibrary::getCurrentDateTime(TimeLibrary::DATE_TIME_FORMAT_YMD);
    }

    /**
     * set cache of coins.
     *
     * @param array $value
     * @return void
     */
    public static function setCoinsCache(array $value): void
    {
        self::setCache(self::getCoinsAllKey(), $value);
    }

    /**
     * get cache of coins.
     *
     * @return ?string
     */
    public static function getCoinsCache(): ?string
    {
        return self::getByKey(self::getCoinsAllKey());
    }

    /**
     * get cache key of events.
     *
     * @return string
     */
    public static function getEventsAllKey(): string
    {
        return self::CACHE_KEY_EVENTS_ALL . '_' . TimeLibrary::getCurrentDateTime(TimeLibrary::DATE_TIME_FORMAT_YMD);
    }

    /**
     * set cache of events.
     *
     * @param array $value
     * @return void
     */
    public static function setEventsCache(array $value): void
    {
        self::setCache(self::getEventsAllKey(), $value);
    }

    /**
     * get cache of events.
     *
     * @return ?string
     */
    public static function getEventsCache(): ?string
    {
        return self::getByKey(self::getEventsAllKey());
    }

    /**
     * get cache key of inoformations.
     *
     * @return string
     */
    public static function getInformationsAllKey(): string
    {
        return self::CACHE_KEY_INFORMATIONS_ALL . '_' . TimeLibrary::getCurrentDateTime(TimeLibrary::DATE_TIME_FORMAT_YMD);
    }

    /**
     * set cache of inoformations.
     *
     * @param array $value
     * @return void
     */
    public static function setInformationsCache(array $value): void
    {
        self::setCache(self::getInformationsAllKey(), $value);
    }

    /**
     * get cache of inoformations.
     *
     * @return ?string
     */
    public static function getInformationsCache(): ?string
    {
        return self::getByKey(self::getInformationsAllKey());
    }

    /**
     * get cache key of service terms.
     *
     * @return string
     */
    public static function getServiceTermsAllKey(): string
    {
        return self::CACHE_KEY_SERVICE_TERMS_ALL . '_' . TimeLibrary::getCurrentDateTime(TimeLibrary::DATE_TIME_FORMAT_YMD);
    }

    /**
     * set cache of service terms.
     *
     * @param array $value
     * @return void
     */
    public static function setServiceTermsCache(array $value): void
    {
        self::setCache(self::getServiceTermsAllKey(), $value);
    }

    /**
     * get cache of service terms.
     *
     * @return ?string
     */
    public static function getServiceTermsCache(): ?string
    {
        return self::getByKey(self::getServiceTermsAllKey());
    }
}
