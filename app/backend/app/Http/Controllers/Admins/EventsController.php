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
use App\Http\Requests\Admin\Events\EventCreateRequest;
use App\Http\Requests\Admin\Events\EventDeleteRequest;
use App\Http\Requests\Admin\Events\EventImportRequest;
use App\Http\Requests\Admin\Events\EventUpdateRequest;
use App\Services\Admins\EventsService;
use App\Trait\CheckHeaderTrait;

class EventsController extends Controller
{
    use CheckHeaderTrait;
    private EventsService $service;

    /**
     * Create a new controller instance.
     *
     * @param EventsService $service
     * @return void
     */
    public function __construct(EventsService $service)
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
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.events'))) {
            throw new MyApplicationHttpException(StatusCodeMessages::STATUS_403);
        }

        // サービスの実行
        return ResponseLibrary::jsonResponse($this->service->getEvents($request));
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
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.events'))) {
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
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.events'))) {
            throw new MyApplicationHttpException(StatusCodeMessages::STATUS_403);
        }

        // サービスの実行
        return $this->service->downloadTemplate();
    }

    /**
     * import record data by file.
     *
     * @param EventImportRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws MyApplicationHttpException
     */
    public function uploadTemplate(EventImportRequest $request): JsonResponse
    {
        // サービスの実行
        $this->service->importTemplate($request->file);
        return ResponseLibrary::jsonResponse(status: StatusCodeMessages::STATUS_201);
    }

    /**
     * creating a new resource.
     *
     * @param  EventCreateRequest  $request
     * @return \Illuminate\Http\JsonResponse
     * @throws MyApplicationHttpException
     */
    public function create(EventCreateRequest $request): JsonResponse
    {
        // サービスの実行
        $this->service->createEvent(
            $request->{EventCreateRequest::KEY_NAME},
            $request->{EventCreateRequest::KEY_TYPE},
            $request->{EventCreateRequest::KEY_DETAIL},
            $request->{EventCreateRequest::KEY_START_AT},
            $request->{EventCreateRequest::KEY_END_AT},
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
     * @param  EventUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     * @throws MyApplicationHttpException
     */
    public function update(EventUpdateRequest $request, int $id): JsonResponse
    {
        // サービスの実行
        $this->service->updateEvent(
            $id,
            $request->{EventUpdateRequest::KEY_NAME},
            $request->{EventUpdateRequest::KEY_TYPE},
            $request->{EventUpdateRequest::KEY_DETAIL},
            $request->{EventUpdateRequest::KEY_START_AT},
            $request->{EventUpdateRequest::KEY_END_AT},
        );
        return ResponseLibrary::jsonResponse();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  EventDeleteRequest  $request
     * @return \Illuminate\Http\JsonResponse
     * @throws MyApplicationHttpException
     */
    public function destroy(EventDeleteRequest $request): JsonResponse
    {
        // サービスの実行
        $this->service->deleteEvent($request->{EventDeleteRequest::KEY_EVENTS});
        return ResponseLibrary::jsonResponse();
    }
}
