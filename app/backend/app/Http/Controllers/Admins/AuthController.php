<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Repositories\Admins\AdminsRoles\AdminsRolesRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Config;

class AuthController extends Controller
{
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
                'password' => Config::get('myapp.seeder.password.testadmin')
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
        $user = auth('api-admins')->user();

        // Tymon\JWTAuth\factory
        // Tymon\JWTAuth\Claims\Factory
        // ユーザー情報を返す。
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api-admins')->factory()->getTTL() * 60,
            'user' => $this->getAdminResource($user)
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
            'id'        => $user->id,
            'name'      => $user->name,
            'authority' => $this->getRoleCode($user->id)
        ];
    }
}
