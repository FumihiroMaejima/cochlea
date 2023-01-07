<?php

namespace App\Library\Session;

use Illuminate\Redis\RedisManager;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redis;
use Predis\Response\Status;
use App\Library\Message\StatusCodeMessages;
use App\Exceptions\MyApplicationHttpException;
use App\Library\File\FileLibrary;
use App\Library\Random\RandomStringLibrary;
use App\Trait\CheckHeaderTrait;

class SessionLibrary
{
    use CheckHeaderTrait;

    public const SESSION_TTL_SECOND = 60; // 60秒

    private const DEFAULT_CACHE_EXPIRE = 86400; // (1日=86400秒)

    private const SET_CACHE_RESULT_VALUE = 'OK';
    private const SET_CACHE_EXPIRE_RESULT_VALUE = 1;

    private const DELETE_CACHE_RESULT_VALUE_SUCCESS = 1;
    private const DELETE_CACHE_RESULT_VALUE_NO_DATA = 0;

    private const REDIS_CONNECTION = 'session';

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

        $cache = Redis::connection(self::REDIS_CONNECTION)->get($key);

        if (is_null($cache)) {
            return $cache;
        }

        if (is_string($cache)) {
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
        // test時は実行しない
        if (Config::get('app.env') !== 'testing') {
            if (is_array($value)) {
                $value = json_encode($value);
            }

            /** @var Status $result redisへの設定処理結果 */
            $result = Redis::connection(self::REDIS_CONNECTION)->set($key, $value);
            $payload = $result->getPayload();

            if ($payload !== self::SET_CACHE_RESULT_VALUE) {
                throw new MyApplicationHttpException(
                    StatusCodeMessages::STATUS_500,
                    'set cache action is failure.'
                );
            }

            // 現在の時刻から$expire秒後のタイムスタンプを期限に設定
            /** @var int $setExpireResult 期限設定処理結果 */
            $setExpireResult = Redis::connection(self::REDIS_CONNECTION)->expireAt($key, time() + $expire);

            if ($setExpireResult !== self::SET_CACHE_EXPIRE_RESULT_VALUE) {
                throw new MyApplicationHttpException(
                    StatusCodeMessages::STATUS_500,
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
                StatusCodeMessages::STATUS_500,
                'cache is not exist.'
            );
        }

        /** @var int $result 削除結果 */
        $result = Redis::connection(self::REDIS_CONNECTION)->del($key);

        if (($result !== self::DELETE_CACHE_RESULT_VALUE_SUCCESS) && !$isIgnore) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_500,
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
        $cache = Redis::connection(self::REDIS_CONNECTION)->get($key);

        return $cache ? true : false;
    }

    /**
     * get token by user id and session id.
     *
     * @param int $userId user id
     * @param ?string $sessionId session id
     * @return ?string token
     */
    public static function getSssionTokenByUserIdAndSessionId(int $userId, ?string $sessionId): ?string
    {
        if (empty($sessionId)) {
            return '';
        }
        // ユーザーIDが設定されていない場合
        $sessionKey = empty($userId) ? 'no_auth_session_id:'.$sessionId : 'session_id:'.$sessionId . ':'. $userId;
        $token = self::getByKey($sessionKey);

        return $token;
    }

    /**
     * get refresh token by user id and session id.
     *
     * @param int $userId user id
     * @param ?string $sessionId session id
     * @return ?string token
     */
    public static function getRefreshTokenByUserIdAndSessionId(int $userId, ?string $sessionId): ?string
    {
        if (empty($sessionId)) {
            return '';
        }
        return self::getByKey('refresh_token_session_id:'.$sessionId . ':'. $userId);
    }

    /**
     * generate session by user id.
     *
     * @param int $userId user id
     * @return string
     */
    public static function generateSessionByUserId(int $userId): string
    {
        $sessionId = RandomStringLibrary::getRandomShuffleString(RandomStringLibrary::RANDOM_STRING_LENGTH_60);
        $token = RandomStringLibrary::getRandomShuffleString(RandomStringLibrary::RANDOM_STRING_LENGTH_60);
        $refreshToken = RandomStringLibrary::getRandomShuffleString(RandomStringLibrary::RANDOM_STRING_LENGTH_60);

        self::setCache('session_id:'.$sessionId . ':'. $userId, $token, 1800);
        self::setCache('refresh_token_session_id:'.$sessionId . ':'. $userId, $refreshToken, 3600);

        return $sessionId;
    }

    /**
     * generate no authenticated session.
     *
     * @return string
     */
    public static function generateNoAuthSession(): string
    {
        // 未ログインユーザー用のセッションの作成
        $noAuthSessionId = RandomStringLibrary::getRandomShuffleString(RandomStringLibrary::RANDOM_STRING_LENGTH_60);
        $token = RandomStringLibrary::getRandomShuffleString(RandomStringLibrary::RANDOM_STRING_LENGTH_60);

        self::setCache('no_auth_session_id:'. $noAuthSessionId, $token, 1800);

        return $noAuthSessionId;
    }

    /**
     * get token by user id and session id.
     *
     * @param string $key session key
     * @return ?string token
     */
    private static function getSssionTokenForTesting(string $key): ?string
    {
        if (empty($key)) {
            return '';
        }

        $directory = Config::get('myappFile.upload.storage.local.teting.session');

        $path = "{$directory}{$key}.txt";

        $file = FileLibrary::getFileStoream($path);
        $token = $file;

        return $token;
    }

    /**
     * get token by user id and session id.
     *
     * @param string $key
     * @param string $value
     * @return void
     */
    private static function setCacheForTesting(string $key, mixed $value): void
    {
        $directory = Config::get('myappFile.upload.storage.local.teting.session');

        $path = "{$directory}{$key}.txt";

        FileLibrary::setTextToFile($path, $value);
    }
}
