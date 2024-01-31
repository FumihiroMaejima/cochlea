<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admins;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Library\Response\ResponseLibrary;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Banners\BannerCreateRequest;
use App\Http\Requests\Admin\Banners\BannerDeleteRequest;
use App\Http\Requests\Admin\Banners\BannerImagesImportRequest;
use App\Http\Requests\Admin\Banners\BannersImportRequest;
use App\Http\Requests\Admin\Banners\BannerUpdateRequest;
use App\Services\Admins\BannersService;
use App\Library\Time\TimeLibrary;
use App\Trait\CheckHeaderTrait;

class BannersController extends Controller
{
    use CheckHeaderTrait;
    private BannersService $service;

    /**
     * Create a new controller instance.
     *
     * @param BannersService $service
     * @return void
     */
    public function __construct(BannersService $service)
    {
        $this->middleware('customAuth:api-admins');
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     * @throws MyApplicationHttpException
     */
    public function index(Request $request): JsonResponse
    {
        // 権限チェック
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.banners'))) {
            throw new MyApplicationHttpException(StatusCodeMessages::STATUS_403);
        }

        // サービスの実行
        // return $this->service->getBanners($request);
        return ResponseLibrary::jsonResponse($this->service->getBanners($request));
    }

    /**
     * Get Banner Image.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $uuid
     * @return BinaryFileResponse
     * @throws MyApplicationHttpException
     */
    public function getImage(Request $request, string $uuid): BinaryFileResponse
    {
        // 権限チェック
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.banners'))) {
            throw new MyApplicationHttpException(StatusCodeMessages::STATUS_403);
        }

        // バリデーションチェック
        $validator = Validator::make(
            ['uuid' => $uuid],
            [
                'uuid' => ['required','uuid'],
            ]
        );

        if ($validator->fails()) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_422,
            );
        }

        // サービスの実行
        return $this->service->getImage($request->uuid);
    }

    /**
     * 画像ファイルイメージのアップロード
     *
     * @param BannerImagesImportRequest $request
     * @param string $uuid
     * @return JsonResponse
     * @throws MyApplicationHttpException
     */
    public function uploadImage(BannerImagesImportRequest $request, string $uuid): JsonResponse
    {
        // サービスの実行
        return $this->service->uploadImage($uuid, $request->image);
    }

    /**
     * download a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws MyApplicationHttpException
     */
    public function download(Request $request): BinaryFileResponse
    {
        // 権限チェック
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.banners'))) {
            throw new MyApplicationHttpException(StatusCodeMessages::STATUS_403);
        }

        // サービスの実行
        return $this->service->downloadCSV();
    }

    /**
     * download import template for import the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     * @throws MyApplicationHttpException
     */
    public function template(Request $request): BinaryFileResponse|JsonResponse
    {
        // 権限チェック
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.banners'))) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        // サービスの実行
        return $this->service->downloadTemplate();
    }

    /**
     * import record data by file.
     *
     * @param BannersImportRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws MyApplicationHttpException
     */
    public function uploadTemplate(BannersImportRequest $request): JsonResponse
    {
        // サービスの実行
        return $this->service->importTemplate($request->file);
    }

    /**
     * creating a new resource.
     *
     * @param  BannerCreateRequest  $request
     * @return \Illuminate\Http\JsonResponse
     * @throws MyApplicationHttpException
     */
    public function create(BannerCreateRequest $request): JsonResponse
    {
        // サービスの実行
        return $this->service->createBanner(
            $request->{BannerCreateRequest::KEY_NAME},
            $request->{BannerCreateRequest::KEY_DETAIL},
            $request->{BannerCreateRequest::KEY_LOCATION},
            $request->{BannerCreateRequest::KEY_PC_HEIGHT},
            $request->{BannerCreateRequest::KEY_PC_WIDTH},
            $request->{BannerCreateRequest::KEY_SP_HEIGHT},
            $request->{BannerCreateRequest::KEY_SP_WIDTH},
            $request->{BannerCreateRequest::KEY_START_AT},
            $request->{BannerCreateRequest::KEY_END_AT},
            $request->{BannerCreateRequest::KEY_URL},
            $request->{BannerCreateRequest::KEY_IMAGE} ?? null
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  BannerUpdateRequest  $request
     * @param  string $uuid
     * @return \Illuminate\Http\JsonResponse
     * @throws MyApplicationHttpException
     */
    public function update(BannerUpdateRequest $request, string $uuid): JsonResponse
    {
        // サービスの実行
        return $this->service->updateBanner(
            $uuid,
            $request->{BannerCreateRequest::KEY_NAME},
            $request->{BannerCreateRequest::KEY_DETAIL},
            $request->{BannerCreateRequest::KEY_LOCATION},
            $request->{BannerCreateRequest::KEY_PC_HEIGHT},
            $request->{BannerCreateRequest::KEY_PC_WIDTH},
            $request->{BannerCreateRequest::KEY_SP_HEIGHT},
            $request->{BannerCreateRequest::KEY_SP_WIDTH},
            $request->{BannerCreateRequest::KEY_START_AT},
            $request->{BannerCreateRequest::KEY_END_AT},
            $request->{BannerCreateRequest::KEY_URL},
            $request->{BannerCreateRequest::KEY_IMAGE} ?? null
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  BannerDeleteRequest  $request
     * @return \Illuminate\Http\JsonResponse
     * @throws MyApplicationHttpException
     */
    public function destroy(BannerDeleteRequest $request): JsonResponse
    {
        // サービスの実行
        return $this->service->deleteBanner($request->{BannerDeleteRequest::KEY_BANNERS});
    }
}
