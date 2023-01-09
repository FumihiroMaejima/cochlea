<?php

namespace App\Http\Middleware;

use Closure;
use App\Library\Session\SessionLibrary;
use App\Library\Message\StatusCodeMessages;
use App\Exceptions\MyApplicationHttpException;
use App\Trait\CheckHeaderTrait;

class CustomAuthenticate
{
    use CheckHeaderTrait;

    private const SESSION_KEY_NAME_USER_PREFIX = 'user_';
    private const SESSION_HEADER_TOKEN_NAME = 'Authorization';
    private const SESSION_HEADER_ID_NAME = 'X-Auth-ID';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        // TODO セッションの有無チェックと新規作成と設定
        // ユーザーIDの照会、レスポンスヘッダーに設定
        $sessionId = self::getSessionId($request);

        if (!empty($guards) && $sessionId) {
            $userId = self::getUserId($request, true);
            $guard = current($guards);
            if (!is_string($guard)) {
                throw new MyApplicationHttpException(
                    StatusCodeMessages::STATUS_500,
                    'Server Error. Guard Setting Error.'
                );
            }

            $token = SessionLibrary::getSssionTokenByUserIdAndSessionId($userId, $sessionId, $guard);

            // トークンが設定されていない場合
            if (empty($token)) {
                // ユーザーIDが設定されていない場合
                if (empty($userId)) {
                    SessionLibrary::generateNoAuthSession();
                } else {
                    // リフレッシュトークンの取得
                    $refreshToken = SessionLibrary::getRefreshTokenByUserIdAndSessionId($userId, $sessionId, $guard);
                    if (empty($refreshToken)) {
                        // リフレッシュトークンも無いならセッション切れエラーとする
                        throw new MyApplicationHttpException(
                            StatusCodeMessages::STATUS_401,
                            'Unauthorized. Session Failure Error.'
                        );
                    }

                    // 新しいセッションの設定
                    SessionLibrary::generateSessionByUserId($userId, $guard);
                }
            }
        } else {
            // 未ログインユーザー用のセッションの作成
            SessionLibrary::generateNoAuthSession();
        }

        return $next($request);
    }
}
