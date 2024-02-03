<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Library\Response\ResponseLibrary;
use App\Services\Admins\RolesService;
use App\Http\Requests\Admin\Roles\RoleCreateRequest;
use App\Http\Requests\Admin\Roles\RoleUpdateRequest;
use App\Http\Requests\Admin\Roles\RoleDeleteRequest;
use App\Trait\CheckHeaderTrait;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class RolesController extends Controller
{
    use CheckHeaderTrait;
    private $service;

    /**
     * Create a new RolesController instance.
     *
     * @return void
     */
    public function __construct(RolesService $rolesService)
    {
        $this->middleware('customAuth:api-admins');
        $this->service = $rolesService;
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
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.roles'))) {
            throw new MyApplicationHttpException(StatusCodeMessages::STATUS_403);
        }

        // サービスの実行
        return ResponseLibrary::jsonResponse($this->service->getRoles($request));
        // return $this->service->getRoles($request);
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     * @throws MyApplicationHttpException
     */
    public function list(Request $request): JsonResponse
    {
        // 権限チェック
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.roles'))) {
            throw new MyApplicationHttpException(StatusCodeMessages::STATUS_403);
        }

        // サービスの実行
        return ResponseLibrary::jsonResponse($this->service->getRolesList($request));
        // return $this->service->getRolesList($request);
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
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.roles'))) {
            throw new MyApplicationHttpException(StatusCodeMessages::STATUS_403);
        }

        // サービスの実行
        return $this->service->downloadCSV($request);
    }

    /**
     * creating a new resource.
     *
     * @param  RoleCreateRequest  $request
     * @return JsonResponse
     * @throws MyApplicationHttpException
     */
    public function create(RoleCreateRequest $request): JsonResponse
    {
        // サービスの実行
        $this->service->createRole($request);
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
     * @param  RoleUpdateRequest  $request
     * @param  int  $id
     * @return JsonResponse
     * @throws MyApplicationHttpException
     */
    public function update(RoleUpdateRequest $request, int $id): JsonResponse
    {
        // サービスの実行
        $this->service->updateRole($id, $request);
        return ResponseLibrary::jsonResponse();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  RoleDeleteRequest  $request
     * @return JsonResponse
     * @throws MyApplicationHttpException
     */
    public function destroy(RoleDeleteRequest $request): JsonResponse
    {
        // サービスの実行
        $this->service->deleteRole($request);
        return ResponseLibrary::jsonResponse();
    }
}
