<?php

namespace App\Trait;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Exceptions\MyApplicationHttpException;
use App\Exceptions\ExceptionStatusCodeMessages;

trait CheckHeaderTrait
{
    /**
     * checkHeader
     *
     * @param Illuminate\Http\Request $request
     * @param array $targets
     * @return boolean
     */
    public function checkRequestAuthority(Request $request, array $targets)
    {
        return in_array($request->header(Config::get('myapp.headers.authority')), $targets, true);
    }

    /**
     * get user id from header
     *
     * @param Illuminate\Http\Request $request
     * @return int
     */
    public function getUserId(Request $request): int
    {
        // ヘッダーから取得した時は文字列になっている。
        $userId = (int)$request->header(Config::get('myapp.headers.id'));

        if (!is_integer($userId) || ($userId <= 0)) {
            throw new MyApplicationHttpException(
                ExceptionStatusCodeMessages::STATUS_CODE_401,
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
        $sessionId = $request->header(Config::get('myapp.headers.passwordReset'));

        if (empty($sessionId)) {
            throw new MyApplicationHttpException(
                ExceptionStatusCodeMessages::STATUS_CODE_401,
                'Invalid header data.'
            );
        }
        return $sessionId;
    }
}
