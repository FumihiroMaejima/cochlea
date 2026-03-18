<?php

declare(strict_types=1);

namespace App\Trait;

use Illuminate\Http\Request;
use App\Exceptions\MyApplicationHttpException;
use App\Library\JWT\JwtLibrary;
use App\Library\Message\StatusCodeMessages;

trait CheckHeaderTrait
{
    /**
     * checkHeader
     *
     * @param Illuminate\Http\Request $request
     * @param array $targets
     * @return boolean
     */
    public function checkRequestAuthority(Request $request, array $targets): bool
    {
        return in_array($request->header(config('myapp.headers.authority')), $targets, true);
    }

    /**
     * get session id from header
     *
     * @param Illuminate\Http\Request $request
     * @return ?string
     */
    public static function getSessionId(Request $request): ?string
    {
        $sessionId = $request->header(config('myapp.headers.authorization'));
        if (!is_string($sessionId) || empty($sessionId)) {
            return null;
        }

        // ヘッダー値のprefixを削除して返す
        return mb_substr($sessionId, mb_strlen(JwtLibrary::TOKEN_PREFIX_WITH_WHITE_SPACE));
    }

    /**
     * get user id from header
     *
     * @param Illuminate\Http\Request $request
     * @param bool $ignoreNoData ignore if no user id.
     * @return int
     */
    public static function getUserId(Request $request, bool $ignoreNoData = false): int
    {
        // ヘッダーから取得した時は文字列になっている。
        $userId = (int)$request->header(config('myapp.headers.id'));

        if (!is_integer($userId) || ($userId <= 0)) {
            if ($ignoreNoData) {
                return 0;
            }
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_401,
                'Invalid header data.'
            );
        }
        return $userId;
    }

    /**
     * get password session id from header
     *
     * @param Illuminate\Http\Request $request
     * @return string
     */
    public function getPasswordResetSessionId(Request $request): string
    {
        $sessionId = $request->header(config('myapp.headers.passwordReset'));

        if (empty($sessionId)) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_401,
                'Invalid header data.'
            );
        }
        return $sessionId;
    }

    /**
     * get faker timestamp from header
     *
     * @param Illuminate\Http\Request $request
     * @param bool $ignoreNoData ignore if no user id.
     * @return ?int
     */
    public static function getFakerTimeStamp(Request $request): ?int
    {
        // ヘッダーから取得した時は文字列になっている。
        $timeStamp = (int)$request->header(config('myapp.headers.fakerTime'));

        if (!is_integer($timeStamp) || ($timeStamp <= 0)) {
            return null;
        }
        return $timeStamp;
    }

    /**
     * get fakertimezone from header
     *
     * @param Illuminate\Http\Request $request
     * @param bool $ignoreNoData ignore if no user id.
     * @return ?string
     */
    public static function getFakerTimeZone(Request $request): ?string
    {
        // ヘッダーから取得した時は文字列になっている。
        $timeZone = $request->header(config('myapp.headers.fakerTimeZone'));

        if (!is_string($timeZone) || empty($timeZone)) {
            return null;
        }
        return $timeZone;
    }
}
