<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\MyApplicationHttpException;
use App\Library\File\PdfLibrary;
use App\Library\Message\StatusCodeMessages;
use App\Library\Time\TimeLibrary;
use App\Http\Controllers\Controller;
use App\Services\Users\DebugService;
use App\Services\Admins\ImagesService;
use App\Trait\CheckHeaderTrait;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
        // 認証が必要なメソッドのみ指定する
        $this->middleware('auth:api-users', ['only' => ['assignCoins']]);
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
     * assign coins.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function assignCoins(Request $request): JsonResponse
    {
        // バリデーションチェック
        $validator = Validator::make(
            $request->all(),
            [
                'freeCoins' => ['int', 'min:1'],
                'paidCoins' => ['int', 'min:1'],
                'limitedTimeCoins' => ['int', 'min:1'],
                'expiredAt' => [
                    'required_with_all:limitedTimeCoins',
                    'date',
                    'date_format:'.TimeLibrary::DEFAULT_DATE_TIME_FORMAT_SLASH,
                    'after:'.TimeLibrary::getCurrentDateTime()
                ],
            ]
        );

        if ($validator->fails()) {
            // $validator->errors()->toArray();
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_422,
                'validation error.',
                $validator->errors()->toArray()
            );
        }

        // ユーザーIDの取得
        $userId = self::getUserId($request);

        // サービスの実行
        return $this->service->assignCoins(
            $userId,
            $request->freeCoins ?? 0,
            $request->paidCoins ?? 0,
            $request->limitedTimeCoins ?? 0,
            $request->expiredAt ?? null,
        );
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

    /**
     * テスト用PDFファイルの表示
     *
     * @param DebugFileUploadRequest $request
     * @return BinaryFileResponse|JsonResponse
     * @throws MyApplicationHttpException
     */
    public function getSamplePDF(): BinaryFileResponse|JsonResponse
    {
        return response()->file(PdfLibrary::getSamplePDF());
    }
}
