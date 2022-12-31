<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Models\Masters\Admins;
use App\Repositories\Admins\AdminsRoles\AdminsRolesRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Config;

class AuthController extends Controller
{
    // login response
    private const LOGIN_RESEPONSE_KEY_ACCESS_TOKEN = 'access_token';
    private const LOGIN_RESEPONSE_KEY_TOKEN_TYPE = 'token_type';
    private const LOGIN_RESEPONSE_KEY_EXPIRES_IN = 'expires_in';
    private const LOGIN_RESEPONSE_KEY_USER = 'user';

    // admin resource key
    private const ADMIN_RESOURCE_KEY_ID = 'id';
    private const ADMIN_RESOURCE_KEY_NAME = 'name';
    private const ADMIN_RESOURCE_KEY_AUTHORITY = 'authority';

    // token prefix
    private const TOKEN_PREFIX = 'bearer';

    private const SESSION_TTL = 60; // 60秒

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Illuminate\Routing\Controller
        $this->middleware('auth:api-admins', ['except' => ['login']]);
    }

    /**
     * ログイン
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        // $credentials = request(['email', 'password']);

        $credentials = [];
        if (Config::get('app.env') === 'production' || Config::get('app.env') === 'testing') {
            $credentials = request(['email', 'password']);
        } else {
            // ローカル開発時はnameだけでログインする。
            $credentials = [
                'name'     => request()->email,
                'password' => Config::get('myappSeeder.seeder.password.testadmin')
            ];
        }

        // auth()がreturnするguard: /tymon/jwt-auth/src/JWTGuard
        if (!$token = auth('api-admins')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
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
        $user = auth('api-admins')->user();
        return response()->json($this->getAdminResource($user));
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
        auth('api-admins')->logout();

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
        return $this->respondWithToken(auth('api-admins')->refresh());
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
        /** @var Admins $user authenticated admin model */
        $user = auth('api-admins')->user();

        $ttl = self::SESSION_TTL * 60;

        // Tymon\JWTAuth\factory
        // Tymon\JWTAuth\Claims\Factory
        // ユーザー情報を返す。
        return response()->json([
            self::LOGIN_RESEPONSE_KEY_ACCESS_TOKEN => $token,
            self::LOGIN_RESEPONSE_KEY_TOKEN_TYPE => self::TOKEN_PREFIX,
            self::LOGIN_RESEPONSE_KEY_EXPIRES_IN => $ttl,
            self::LOGIN_RESEPONSE_KEY_USER => $this->getAdminResource($user)
        ]);
    }

    /**
     * 管理者のロールIDを取得
     *
     * @param  int $id
     * @return array
     */
    protected function getRoleCode(int $adminId): array
    {
        return app()->make(AdminsRolesRepositoryInterface::class)->getByAdminId($adminId)
            ->pluck('code')
            ->values()
            ->toArray();
    }

    /**
     * 管理者情報のリソースを取得
     *
     * @param Authenticatable $user
     * @return array
     */
    protected function getAdminResource(Authenticatable $user): array
    {
        return [
            self::ADMIN_RESOURCE_KEY_ID        => $user->id,
            self::ADMIN_RESOURCE_KEY_NAME      => $user->name,
            self::ADMIN_RESOURCE_KEY_AUTHORITY => $this->getRoleCode($user->id)
        ];
    }
}
