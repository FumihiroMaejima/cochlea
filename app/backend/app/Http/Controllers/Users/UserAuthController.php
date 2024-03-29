<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Http\Controllers\Controller;
use App\Services\Users\UserAuthService;
use App\Trait\CheckHeaderTrait;

class UserAuthController extends Controller
{
    use CheckHeaderTrait;
    private UserAuthService $service;

    /**
     * Create a Controller instance.
     *
     * @param UserAuthService $userAuthService
     * @return void
     */
    public function __construct(UserAuthService $userAuthService)
    {
        $this->middleware('auth:api-users', ['except' => ['registerByEmail']]);
        $this->service = $userAuthService;
    }

    /**
     * register user by email.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function registerByEmail(Request $request): JsonResponse
    {
        // バリデーションチェック
        $validator = Validator::make(
            $request->all(),
            [
                'email' => ['required', 'string', 'email:rfc', 'between:1,50'],
            ]
        );


        if ($validator->fails()) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_422,
                'validation error.',
                $validator->errors()->toArray()
            );
        }

        // サービスの実行
        return $this->service->registUserByEmailAndSendAuthCode($request->email);
    }

    /**
     * register user by email.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function valdiateAuthCode(Request $request): JsonResponse
    {
        // バリデーションチェック
        $validator = Validator::make(
            $request->all(),
            [
                'code' => ['required', 'int', 'min:1', 'max:999999'],
            ]
        );
        // TODO with session.

        if ($validator->fails()) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_422,
                'validation error.',
                $validator->errors()->toArray()
            );
        }

        // ユーザーIDの取得
        $userId = $this->getUserId($request);

        // サービスの実行
        return $this->service->validateUserAuthCode($userId, (int)$request->code);
    }

    /**
     * leave from service.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function leaveUser(Request $request): JsonResponse
    {
        // ユーザーIDの取得
        $userId = $this->getUserId($request);

        // サービスの実行
        return $this->service->leaveUser($userId);
    }
}
