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
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.informations'))) {
            throw new MyApplicationHttpException(StatusCodeMessages::STATUS_403);
        }

        // サービスの実行
        // return $this->service->getInformations($request);
        return ResponseLibrary::jsonResponse($this->service->getInformations($request));
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
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.informations'))) {
            throw new MyApplicationHttpException(StatusCodeMessages::STATUS_403);
        }

        // サービスの実行
        return $this->service->downloadCSV();
    }

    /**
     * download import template for import the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws MyApplicationHttpException
     */
    public function template(Request $request): BinaryFileResponse
    {
        // 権限チェック
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.informations'))) {
            throw new MyApplicationHttpException(StatusCodeMessages::STATUS_403);
        }

        // サービスの実行
        return $this->service->downloadTemplate();
    }

    /**
     * import record data by file.
     *
     * @param InformationImportRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws MyApplicationHttpException
     */
    public function uploadTemplate(InformationImportRequest $request): JsonResponse
    {
        // サービスの実行
        $this->service->importTemplate($request->file);
        return ResponseLibrary::jsonResponse(status: StatusCodeMessages::STATUS_201);
    }

    /**
     * creating a new resource.
     *
     * @param  InformationCreateRequest  $request
     * @return \Illuminate\Http\JsonResponse
     * @throws MyApplicationHttpException
     */
    public function create(InformationCreateRequest $request): JsonResponse
    {
        // サービスの実行
        $this->service->createInformation(
            $request->{InformationCreateRequest::KEY_NAME},
            $request->{InformationCreateRequest::KEY_TYPE},
            $request->{InformationCreateRequest::KEY_DETAIL},
            $request->{InformationCreateRequest::KEY_START_AT},
            $request->{InformationCreateRequest::KEY_END_AT},
        );
        return ResponseLibrary::jsonResponse(status: StatusCodeMessages::STATUS_201);
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
     * @throws MyApplicationHttpException
     */
    public function update(InformationUpdateRequest $request, int $id): JsonResponse
    {
        // サービスの実行
        $this->service->updateInformation(
            $id,
            $request->{InformationCreateRequest::KEY_NAME},
            $request->{InformationCreateRequest::KEY_TYPE},
            $request->{InformationCreateRequest::KEY_DETAIL},
            $request->{InformationCreateRequest::KEY_START_AT},
            $request->{InformationCreateRequest::KEY_END_AT},
        );
        return ResponseLibrary::jsonResponse();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  InformationDeleteRequest  $request
     * @return \Illuminate\Http\JsonResponse
     * @throws MyApplicationHttpException
     */
    public function destroy(InformationDeleteRequest $request): JsonResponse
    {
        // サービスの実行
        $this->service->deleteInformation($request->{InformationCreateRequest::KEY_INFORMATIONS});
        return ResponseLibrary::jsonResponse();
    }
}
