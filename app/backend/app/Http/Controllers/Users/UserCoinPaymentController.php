<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\MyApplicationHttpException;
use App\Exceptions\ExceptionStatusCodeMessages;
use App\Http\Controllers\Controller;
use App\Services\Users\UserCoinPaymentService;
use App\Trait\CheckHeaderTrait;

class UserCoinPaymentController extends Controller
{
    use CheckHeaderTrait;
    private UserCoinPaymentService $service;

    /**
     * Create a new AdminDebugController instance.
     *
     * @param UserCoinPaymentService $debugService
     * @return void
     */
    public function __construct(UserCoinPaymentService $userCoinPaymentService)
    {
        $this->middleware('auth:api-users');
        // $this->middleware('auth:api-users', ['except' => ['login']]); // 未ログイン時も実行可能
        $this->service = $userCoinPaymentService;
    }

    /**
     * Create Stipe Checkout Session.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function checkout(Request $request): JsonResponse
    {
        // サービスの実行
        return $this->service->getCheckout($request);
    }

    /**
     * Cancel Stipe Checkout Session.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function cancel(Request $request): JsonResponse
    {
        // バリデーションチェック
        $validator = Validator::make(
            $request->all(),
            [
                'orderId' => ['required','uuid'],
            ]
        );

        if ($validator->fails()) {
            // $validator->errors()->toArray();
            throw new MyApplicationHttpException(
                ExceptionStatusCodeMessages::STATUS_CODE_422,
            );
        }

        // サービスの実行
        return $this->service->cancelCheckout($request->orderId);
    }

    /**
     * Complete Stipe Checkout Session.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function complete(Request $request): JsonResponse
    {
        // バリデーションチェック
        $validator = Validator::make(
            $request->all(),
            [
                'orderId' => ['required','uuid'],
            ]
        );

        if ($validator->fails()) {
            // $validator->errors()->toArray();
            throw new MyApplicationHttpException(
                ExceptionStatusCodeMessages::STATUS_CODE_422,
            );
        }

        // サービスの実行
        return $this->service->completeCheckout($request->orderId);
    }
}
