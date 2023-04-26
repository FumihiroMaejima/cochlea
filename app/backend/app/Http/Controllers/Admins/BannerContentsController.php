<?php

namespace App\Http\Controllers\Admins;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BannerBlocks\BannerBlocksImportRequest;
use App\Http\Requests\Admin\BannerBlocks\BannerBlockContentsImportRequest;
use App\Services\Admins\BannerContentsService;
use App\Trait\CheckHeaderTrait;

class BannerContentsController extends Controller
{
    use CheckHeaderTrait;
    private BannerContentsService $service;

    /**
     * Create a new controller instance.
     *
     * @param BannerContentsService $service
     * @return void
     */
    public function __construct(BannerContentsService $service)
    {
        $this->middleware('customAuth:api-admins');
        $this->service = $service;
    }

    /**
     * download a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function downloadBannerBlocks(Request $request): BinaryFileResponse|JsonResponse
    {
        // 権限チェック
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.home'))) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        // サービスの実行
        return $this->service->downloadCSVForBannerBlocks();
    }

    /**
     * download import template for import the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function templateBannerBlocks(Request $request): BinaryFileResponse|JsonResponse
    {
        // 権限チェック
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.home'))) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        // サービスの実行
        return $this->service->downloadTemplateForBannerBlocks();
    }

    /**
     * import banner blocks data by file.
     *
     * @param BannerBlocksImportRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadTemplateBannerBlocks(BannerBlocksImportRequest $request): JsonResponse
    {
        // サービスの実行
        return $this->service->importTemplateForBannerBlocks($request->file);
    }

    /**
     * download a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function downloadBannerBlockContents(Request $request): BinaryFileResponse|JsonResponse
    {
        // 権限チェック
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.home'))) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        // サービスの実行
        return $this->service->downloadCSVForBannerBlockContents();
    }

    /**
     * download import template for import the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function templateBannerBlockContents(Request $request): BinaryFileResponse|JsonResponse
    {
        // 権限チェック
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.home'))) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        // サービスの実行
        return $this->service->downloadTemplateForBannerBlockContents();
    }

    /**
     * import banner block contents data by file.
     *
     * @param BannerBlockContentsImportRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadTemplateBannerBlockContents(BannerBlockContentsImportRequest $request): JsonResponse
    {
        // サービスの実行
        return $this->service->importTemplateForBannerBlockContents($request->file);
    }
}
