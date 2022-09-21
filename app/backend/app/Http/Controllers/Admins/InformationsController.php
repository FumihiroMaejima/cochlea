<?php

namespace App\Http\Controllers\Admins;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Informations\InformationCreateRequest;
use App\Http\Requests\Admin\Informations\InformationDeleteRequest;
use App\Http\Requests\Admin\Informations\InformationImportRequest;
use App\Http\Requests\Admin\Informations\InformationUpdateRequest;
use App\Services\Admins\InformationsService;
use App\Trait\CheckHeaderTrait;

class InformationsController extends Controller
{
    use CheckHeaderTrait;
    private InformationsService $service;

    /**
     * Create a new controller instance.
     *
     * @param InformationsService $service
     * @return void
     */
    public function __construct(InformationsService $service)
    {
        $this->middleware('auth:api-admins');
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // 権限チェック
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.informations'))) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        // サービスの実行
        return $this->service->getInformations($request);
    }

    /**
     * download a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function download(Request $request): BinaryFileResponse|JsonResponse
    {
        // 権限チェック
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.informations'))) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        // サービスの実行
        return $this->service->downloadCSV($request);
    }

    /**
     * download import template for import the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function template(Request $request): BinaryFileResponse|JsonResponse
    {
        // 権限チェック
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.informations'))) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        // サービスの実行
        return $this->service->downloadTemplate();
    }

    /**
     * import coin data by file.
     *
     * @param InformationImportRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadTemplate(InformationImportRequest $request): JsonResponse
    {
        // サービスの実行
        return $this->service->importTemplate($request->file);
    }

    /**
     * creating a new resource.
     *
     * @param  InformationCreateRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(InformationCreateRequest $request): JsonResponse
    {
        // サービスの実行
        return $this->service->createInformation($request);
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
     * @param  InformationUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(InformationUpdateRequest $request, int $id): JsonResponse
    {
        // サービスの実行
        return $this->service->updateInformation($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  InformationDeleteRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(InformationDeleteRequest $request): JsonResponse
    {
        // サービスの実行
        return $this->service->deleteInformation($request);
    }
}
