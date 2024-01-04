<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admins;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\MyApplicationHttpException;
use App\Library\File\PdfLibrary;
use App\Library\Message\StatusCodeMessages;
use App\Library\Time\TimeLibrary;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Debug\DebugFileUploadRequest;
use App\Services\Admins\DebugService;
use App\Services\Admins\ImagesService;
use App\Trait\CheckHeaderTrait;
use \Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdminDebugController extends Controller
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
     * デバッグ関連情報取得
     *
     * @param Request $request
     * @return JsonResponse
     * @throws MyApplicationHttpException
     */
    public function getDebugStatus(Request $request): JsonResponse
    {
        // 権限チェック
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.debug'))) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $userId = self::getUserId($request);
        $sessionId = self::getSessionId($request);
        $fakerTimeStamp = self::getFakerTimeStamp($request);
        return $this->service->getDebugStatus(
            $userId,
            $sessionId,
            $fakerTimeStamp,
            $request->getClientIp(),
            $request->userAgent()
        );
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
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        // サービスの実行
        return $this->service->getList($request);
    }

    /**
     * 画像ファイルイメージの表示
     *
     * @param DebugFileUploadRequest $request
     * @return BinaryFileResponse|JsonResponse
     * @throws MyApplicationHttpException
     */
    public function getImage(Request $request): BinaryFileResponse|JsonResponse
    {
        // 権限チェック
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.debug'))) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        // バリデーションチェック
        $validator = Validator::make(
            $request->all(),
            [
                'uuid' => ['required','uuid'],
                'ver' => ['int', 'min:0'], // タイムスタンプ
            ]
        );

        if ($validator->fails()) {
            // $validator->errors()->toArray();
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_422,
            );
        }

        // サービスの実行
        return $this->imagesService->getImage(
            $request->uuid,
            $request->ver ?? TimeLibrary::strToTimeStamp(TimeLibrary::getCurrentDateTime())
        );
    }

    /**
     * 画像ファイルイメージのアップロード
     *
     * @param DebugFileUploadRequest $request
     * @return JsonResponse
     */
    public function uploadImage(DebugFileUploadRequest $request): JsonResponse
    {
        // 権限チェック
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.debug'))) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        // サービスの実行
        return $this->imagesService->uploadImage($request);
    }

    /**
     * テスト用PDFファイルの表示
     *
     * @param Request $request
     * @return BinaryFileResponse|JsonResponse
     * @throws MyApplicationHttpException
     */
    public function getSamplePDF(Request $request): BinaryFileResponse|JsonResponse
    {
        // 権限チェック
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.debug'))) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        return response()->file(PdfLibrary::getSamplePDF());
    }
}
