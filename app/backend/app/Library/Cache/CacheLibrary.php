<?php

namespace App\Library\Cache;

use Illuminate\Redis\RedisManager;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redis;
use Predis\Response\Status;
use App\Exceptions\ExceptionStatusCodeMessages;
use App\Exceptions\MyApplicationHttpException;
use App\Trait\CheckHeaderTrait;

class CacheLibrary
{
    use CheckHeaderTrait;

    private const DEFAULT_CACHE_EXPIRE = 86400; // (1日=86400秒)

    private const SET_CACHE_RESULT_VALUE = 'OK';
    private const SET_CACHE_EXPIRE_RESULT_VALUE = 1;

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
        if (Config::get('app.env') === 'testing') {
            return null;
        }

        $cache = Redis::get($key);

        if (is_null($cache)) {
            return $cache;
        }

        if (is_string($cache) || is_integer($cache)) {
            return $cache;
        }

        return json_decode($cache, true);
    }

    /**
     * set cache to redis.
     *
     * @param string $key
     * @param mixed $value
     * @param int $expire
     * @return void
     */
    public static function setCache(string $key, mixed $value, int $expire = self::DEFAULT_CACHE_EXPIRE): void
    {
        // test時は時効しない
        if (Config::get('app.env') !== 'testing') {
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

            // 現在の時刻から$expire秒後のタイムスタンプを期限に設定
            /** @var int $setExpireResult 毅然設定処理結果 */
            $setExpireResult = Redis::expireAt($key, time() + $expire);

            if ($setExpireResult !== self::SET_CACHE_EXPIRE_RESULT_VALUE) {
                throw new MyApplicationHttpException(
                    ExceptionStatusCodeMessages::STATUS_CODE_500,
                    'set cache expire action is failure.'
                );
            }
        }
    }

    /**
     * remove cache by request header data.
     *
     * @param string $key
     * @param bool $isIgnore ignore data check result.
     * @return bool
     */
    public static function deleteCache(string $key, bool $isIgnore = false): void
    {
        $cache = self::getByKey($key);

        if (empty($cache)) {
            if ($isIgnore || (Config::get('app.env') === 'testing')) {
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
