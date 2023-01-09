<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use App\Library\Array\ArrayLibrary;
use App\Library\Session\SessionLibrary;
use App\Library\Message\StatusCodeMessages;
use App\Library\Random\RandomStringLibrary;
use App\Exceptions\MyApplicationHttpException;
use App\Trait\CheckHeaderTrait;
use DateTimeInterface;

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

    /**
     * Determine if the user is logged in to any of the given guards.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $guards
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    protected function authenticate($request, array $guards)
    {
        if (empty($guards)) {
            $guards = [null];
        }

        /* foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->check()) {
                return $this->auth->shouldUse($guard);
            }
        } */

        self::unauthenticated($request, $guards);
    }

    /**
     * Handle an unauthenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $guards
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    private static function unauthenticated($request, array $guards)
    {
        throw new MyApplicationHttpException(
            StatusCodeMessages::STATUS_401,
            'Unauthorized. Session Failure Error.'
        );
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    /* public function handleTmp($request, Closure $next)
    {
        if (! $this->sessionConfigured()) {
            return $next($request);
        }

        $session = $this->getSession($request);
        // TODO セッションの有無チェックと新規作成と設定
        // ユーザーIDの照会、レスポンスヘッダーに設定
        $sessionId = self::getSessionId($request);

        // true内の処理はcontrollerで適用させるmiddlewareで実行
        if ($sessionId) {
            $userId = self::getUserId($request, true);

            // ユーザーIDが設定されていない場合
            $sessionKey = empty($userId) ? 'no_auth_session_id:'.$sessionId : 'session_id:'.$sessionId . ':'. $userId;
            $token = SessionLibrary::getByKey($sessionKey);


            // トークンが設定されていない場合
            if (empty($token)) {
                // ユーザーIDが設定されていない場合
                if (empty($userId)) {
                    $newSessionId = RandomStringLibrary::getRandomShuffleString(RandomStringLibrary::RANDOM_STRING_LENGTH_60);
                    $token = RandomStringLibrary::getRandomShuffleString(RandomStringLibrary::RANDOM_STRING_LENGTH_60);

                    SessionLibrary::setCache('no_auth_session_id:'. $newSessionId, $token, 1800);
                } else {
                    // リフレッシュトークンの取得
                    $refreshToken = SessionLibrary::getByKey('refresh_token_session_id:'.$sessionId . ':'. $userId);
                    if (empty($refreshToken)) {
                        // リフレッシュトークンも無いならセッション切れエラーとする
                        throw new MyApplicationHttpException(
                            StatusCodeMessages::STATUS_401,
                            'Unauthorized. Session Failure Error.'
                        );
                    }

                    $newSessionId = RandomStringLibrary::getRandomShuffleString(RandomStringLibrary::RANDOM_STRING_LENGTH_60);
                    $newToken = RandomStringLibrary::getRandomShuffleString(RandomStringLibrary::RANDOM_STRING_LENGTH_60);
                    $newRefreshToken = RandomStringLibrary::getRandomShuffleString(RandomStringLibrary::RANDOM_STRING_LENGTH_60);
                    SessionLibrary::setCache('session_id:'.$newSessionId . ':'. $userId, $newToken, 1800);
                    SessionLibrary::setCache('refresh_token_session_id:'.$newSessionId . ':'. $userId, $newRefreshToken, 1800);
                }
            }
        } else {
            // 未ログインユーザー用のセッションの作成
            $noAuthSessionId = RandomStringLibrary::getRandomShuffleString(RandomStringLibrary::RANDOM_STRING_LENGTH_60);
            $token = RandomStringLibrary::getRandomShuffleString(RandomStringLibrary::RANDOM_STRING_LENGTH_60);

            SessionLibrary::setCache('no_auth_session_id:'. $noAuthSessionId, $token, 1800);
        }

        if ($this->manager->shouldBlock() ||
            ($request->route() instanceof Route && $request->route()->locksFor())) {
            return $this->handleRequestWhileBlocking($request, $session, $next);
        }

        return $this->handleStatefulRequest($request, $session, $next);
    } */

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }
}
