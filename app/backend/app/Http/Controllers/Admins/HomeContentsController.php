<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admins;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Library\Response\ResponseLibrary;
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
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws MyApplicationHttpException
     */
    public function downloadHomeContentsGroups(Request $request): BinaryFileResponse
    {
        // 権限チェック
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.home'))) {
            throw new MyApplicationHttpException(StatusCodeMessages::STATUS_403);
        }

        // サービスの実行
        return $this->service->downloadCSVForHomeContentsGroups();
    }

    /**
     * download import template for import the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws MyApplicationHttpException
     */
    public function templateHomeContentsGroups(Request $request): BinaryFileResponse
    {
        // 権限チェック
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.home'))) {
            throw new MyApplicationHttpException(StatusCodeMessages::STATUS_403);
        }

        // サービスの実行
        return $this->service->downloadTemplateForHomeContentsGroups();
    }

    /**
     * import coin data by file.
     *
     * @param HomeContentsGroupsImportRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws MyApplicationHttpException
     */
    public function uploadTemplateHomeContentsGroups(HomeContentsGroupsImportRequest $request): JsonResponse
    {
        // サービスの実行
        $this->service->importTemplateForHomeContentsGroups($request->file);
        return ResponseLibrary::jsonResponse(status: StatusCodeMessages::STATUS_201);
    }

    /**
     * download a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws MyApplicationHttpException
     */
    public function downloadHomeContents(Request $request): BinaryFileResponse
    {
        // 権限チェック
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.home'))) {
            throw new MyApplicationHttpException(StatusCodeMessages::STATUS_403);
        }

        // サービスの実行
        return $this->service->downloadCSVForHomeContents();
    }

    /**
     * download import template for import the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws MyApplicationHttpException
     */
    public function templateHomeContents(Request $request): BinaryFileResponse
    {
        // 権限チェック
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.home'))) {
            throw new MyApplicationHttpException(StatusCodeMessages::STATUS_403);
        }

        // サービスの実行
        return $this->service->downloadTemplateForHomeContents();
    }

    /**
     * import record data by file.
     *
     * @param HomeContentsImportRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws MyApplicationHttpException
     */
    public function uploadTemplateHomeContents(HomeContentsImportRequest $request): JsonResponse
    {
        // サービスの実行
        $this->service->importTemplateForHomeContents($request->file);
        return ResponseLibrary::jsonResponse(status: StatusCodeMessages::STATUS_201);
    }
}
