<?php

namespace App\Library;

use Illuminate\Http\Request;
use App\Trait\CheckHeaderTrait;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;

class SessionLibrary
{
    use CheckHeaderTrait;

    private const SESSION_KEY_NAME_USER_PREFIX = 'user_';
    private const SESSION_HEADER_TOKEN_NAME = 'Authorization';
    private const SESSION_HEADER_ID_NAME = 'X-Auth-ID';

    // ヘッダーデータのkey名
    private const HEADER_ARRAY_KEY_NAME_ID = 'userId';
    private const HEADER_ARRAY_KEY_NAME_TOKEN = 'token';

    /**
     * check session by request and exit session value.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $value
     * @return bool
     */
    public static function checkSession(Request $request): bool
    {
        $headers = self::getHeaderData($request);

        $sessionValue = json_decode(
            self::getSessionByKey(self::getSessionKey($headers[self::HEADER_ARRAY_KEY_NAME_ID]))
        )->{self::HEADER_ARRAY_KEY_NAME_TOKEN};

        return Hash::check(
            $headers[self::HEADER_ARRAY_KEY_NAME_TOKEN],
            $sessionValue
        );
    }

    /**
     * check session has session key.
     *
     * @param string $key
     * @param string $value
     * @return bool
     */
    public static function checkSessionByKey(string $key, string $value): bool
    {
        return Hash::check($value, Redis::get($key));
    }

    /**
     * check session has session key.
     *
     * @param string $key
     * @return bool
     */
    public static function hasSession(string $key): bool
    {
        $session = Redis::get($key);

        return $session ? true : false;
    }

    /**
     * get session value.
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public static function getSession(Request $request): string
    {
        return self::getSessionByKey(
            self::getSessionKey(
                self::getHeaderData($request)[self::HEADER_ARRAY_KEY_NAME_ID]
            )
        );
    }

    /**
     * get session value by Key.
     *
     * @param string $key
     * @return string
     */
    public static function getSessionByKey(string $key): string
    {
        $session = Redis::get($key);

        return $session ?? '';
    }

    /**
     * remove session by request header data.
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public static function removeSession(Request $request): void
    {
        $key = self::getSessionKey(
            self::getHeaderData($request)[self::HEADER_ARRAY_KEY_NAME_ID]
        );

        Redis::del($key);
    }

    /**
     * start session.
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public static function startSession(Request $request): void
    {
        $headers = self::getHeaderData($request);

        self::setSession(
            self::getSessionKey($headers[self::HEADER_ARRAY_KEY_NAME_ID]),
            $headers[self::HEADER_ARRAY_KEY_NAME_TOKEN]
        );
    }

    /**
     * set session to redis.
     *
     * @param string $key
     * @param string $value
     * @return bool
     */
    public static function setSession(string $key, string $value): void
    {
        $hashedValue = self::hashSession($value);
        Redis::set($key, json_encode(
            [
                self::HEADER_ARRAY_KEY_NAME_TOKEN => $hashedValue
            ]
        ));
    }

    /**
     * hash session data.
     *
     * @param string $value
     * @return string
     */
    private static function hashSession(string $value): string
    {
        // hash化
        return Hash::make(
            $value,
            [
                'rounds' => 12,
            ]
        );
    }

    /**
     * get session key strings.
     *
     * @param int $userId
     * @return string
     */
    private static function getSessionKey(int $userId): string
    {
        return self::SESSION_KEY_NAME_USER_PREFIX . $userId;
    }

    /**
     * get header data.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    private static function getHeaderData(Request $request): array
    {
        return [
            self::HEADER_ARRAY_KEY_NAME_ID    => $request->header(self::SESSION_HEADER_ID_NAME),
            self::HEADER_ARRAY_KEY_NAME_TOKEN => $request->header(self::SESSION_HEADER_TOKEN_NAME),
        ];
    }
}
