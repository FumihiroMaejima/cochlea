<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Http\Controllers\Controller;
use App\Services\Users\DebugService;
use App\Services\Admins\ImagesService;
use App\Trait\CheckHeaderTrait;

class DebugController extends Controller
{
    use CheckHeaderTrait;
    private DebugService $service;
    private ImagesService $imagesService;

    /**
     * Create a new AdminDebugController instance.
     *
     * @param DebugService $debugService
     * @param ImagesService $imagesService
     * @return void
     */
    public function __construct(DebugService $debugService, ImagesService $imagesService)
    {
        $this->service = $debugService;
        $this->imagesService = $imagesService;
    }

    /**
     * Display test Debug message.
     *
     * @return JsonResponse
     */
    public function test(): JsonResponse
    {
        return response()->json(['message' => 'test debug message.'], 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function cancelCheckout(Request $request): JsonResponse
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
                StatusCodeMessages::STATUS_422,
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
    public function completeCheckout(Request $request): JsonResponse
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
                StatusCodeMessages::STATUS_422,
            );
        }

        // サービスの実行
        return $this->service->completeCheckout($request->orderId);
    }

    /**
     * Complete Stipe Checkout Session.
     *
     * @return JsonResponse
     */
    public function debugRandomValue(): JsonResponse
    {
        return $this->service->gacha();
    }
}
