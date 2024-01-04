<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admins;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceTerms\ServiceTermsImportRequest;
use App\Services\Admins\ServiceTermsService;
use App\Trait\CheckHeaderTrait;

class ServiceTermsController extends Controller
{
    use CheckHeaderTrait;
    private ServiceTermsService $service;

    /**
     * Create a new controller instance.
     *
     * @param ServiceTermsService $service
     * @return void
     */
    public function __construct(ServiceTermsService $service)
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
    public function downloadServiceTerms(Request $request): BinaryFileResponse|JsonResponse
    {
        // 権限チェック
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.serviceTerms'))) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        // サービスの実行
        return $this->service->downloadCSVForServiceTerms();
    }

    /**
     * download import template for import the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function templateServiceTerms(Request $request): BinaryFileResponse|JsonResponse
    {
        // 権限チェック
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.serviceTerms'))) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        // サービスの実行
        return $this->service->downloadTemplateForServiceTerms();
    }

    /**
     * import record data by file.
     *
     * @param ServiceTermsImportRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadTemplateServiceTerms(ServiceTermsImportRequest $request): JsonResponse
    {
        // サービスの実行
        return $this->service->importTemplateForServiceTerms($request->file);
    }
}
