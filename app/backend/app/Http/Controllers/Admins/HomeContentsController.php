<?php

namespace App\Http\Controllers\Admins;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HomeContents\HomeContentsGroupsImportRequest;
use App\Http\Requests\Admin\HomeContents\HomeContentsImportRequest;
use App\Services\Admins\HomeContentsService;
use App\Trait\CheckHeaderTrait;

class HomeContentsController extends Controller
{
    use CheckHeaderTrait;
    private HomeContentsService $service;

    /**
     * Create a new controller instance.
     *
     * @param HomeContentsService $service
     * @return void
     */
    public function __construct(HomeContentsService $service)
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
    public function downloadHomeContentsGroups(Request $request): BinaryFileResponse|JsonResponse
    {
        // 権限チェック
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.home'))) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        // サービスの実行
        return $this->service->downloadCSVForHomeContentsGroups();
    }

    /**
     * download import template for import the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function templateHomeContentsGroups(Request $request): BinaryFileResponse|JsonResponse
    {
        // 権限チェック
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.home'))) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        // サービスの実行
        return $this->service->downloadTemplateForHomeContentsGroups();
    }

    /**
     * import coin data by file.
     *
     * @param HomeContentsGroupsImportRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadTemplateHomeContentsGroups(HomeContentsGroupsImportRequest $request): JsonResponse
    {
        // サービスの実行
        return $this->service->importTemplateForHomeContentsGroups($request->file);
    }

    /**
     * download a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function downloadHomeContents(Request $request): BinaryFileResponse|JsonResponse
    {
        // 権限チェック
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.home'))) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        // サービスの実行
        return $this->service->downloadCSVForHomeContents();
    }

    /**
     * download import template for import the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function templateHomeContents(Request $request): BinaryFileResponse|JsonResponse
    {
        // 権限チェック
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.home'))) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        // サービスの実行
        return $this->service->downloadTemplateForHomeContents();
    }

    /**
     * import coin data by file.
     *
     * @param HomeContentsImportRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadTemplateHomeContents(HomeContentsImportRequest $request): JsonResponse
    {
        // サービスの実行
        return $this->service->importTemplateForHomeContents($request->file);
    }
}
