<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Library\Time\TimeLibrary;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    // login response
    private const LOGIN_RESEPONSE_KEY_ACCESS_TOKEN = 'access_token';
    private const LOGIN_RESEPONSE_KEY_TOKEN_TYPE = 'token_type';
    private const LOGIN_RESEPONSE_KEY_EXPIRES_IN = 'expires_in';
    private const LOGIN_RESEPONSE_KEY_USER = 'user';

    // user resource key
    private const USER_RESOURCE_KEY_ID = 'id';
    private const USER_RESOURCE_KEY_NAME = 'name';

    // token prefix
    private const TOKEN_PREFIX = 'bearer';

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Illuminate\Routing\Controller
        $this->middleware('auth:api-users', ['except' => ['login']]);
    }

    /**
     * ログイン
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);
        // $credentials = request(['name', 'password']);

        if (!$token = auth('api-users')->attempt($credentials)) {
            new MyApplicationHttpException(
                StatusCodeMessages::STATUS_401,
                'Unauthorized',
                isResponseMessage: true,
            );
        }

        return $this->respondWithToken($token);
    }

    /**
     * ログインユーザーの情報を取得
     * @header Accept application/json
     * @header Authorization Bearer
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAuthUser()
    {
        return response()->json(auth()->user());
    }

    /**
     * ログアウト
     * @header Accept application/json
     * @header Authorization Bearer
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * トークンのリフレッシュ
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        // Tymon\JWTAuth\JWT
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * レスポンスデータの作成
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        $user = auth('api-users')->user();

        // ログイン日時を更新
        (new User())->updateLastLoginAt($user->id, TimeLibrary::getCurrentDateTime());

        // Tymon\JWTAuth\factory
        // Tymon\JWTAuth\Claims\Factory
        // ユーザー情報を返す。
        return response()->json([
            self::LOGIN_RESEPONSE_KEY_ACCESS_TOKEN => $token,
            self::LOGIN_RESEPONSE_KEY_TOKEN_TYPE   => self::TOKEN_PREFIX,
            self::LOGIN_RESEPONSE_KEY_EXPIRES_IN   => auth('api-users')->factory()->getTTL() * 60,
            self::LOGIN_RESEPONSE_KEY_USER         => [
                self::USER_RESOURCE_KEY_ID   => $user->id,
                self::USER_RESOURCE_KEY_NAME => $user->name
            ]
        ]);
    }
}
