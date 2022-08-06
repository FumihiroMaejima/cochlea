<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
// use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Services\Admins\AdminsService;
use App\Http\Requests\Admins\AdminCreateRequest;
use App\Http\Requests\Admins\AdminDeleteRequest;
use App\Http\Requests\Admins\AdminUpdateRequest;
use App\Http\Requests\Admins\AdminUpdatePasswordRequest;
use App\Http\Requests\Admins\AdminForgotPasswordRequest;
use App\Http\Requests\Admins\AdminResetPasswordRequest;
use App\Trait\CheckHeaderTrait;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdminsController extends Controller
{
    use CheckHeaderTrait;
    private AdminsService $service;

    /**
     * Create a new MembersController instance.
     *
     * @param App\Services\AdminsService $adminsService
     * @return void
     */
    public function __construct(AdminsService $adminsService)
    {
        $this->middleware('auth:api-admins', ['except' => ['forgotPassword', 'resetPassword']]);
        $this->service = $adminsService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // 権限チェック
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.admins'))) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        return $this->service->getAdmins($request);
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function download(Request $request): BinaryFileResponse|JsonResponse
    {
        // 権限チェック
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.admins'))) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        // サービスの実行
        return $this->service->downloadCSV($request);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  AdminCreateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(AdminCreateRequest $request): JsonResponse
    {
        return $this->service->createAdmin($request);
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
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  AdminUpdateRequest $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(AdminUpdateRequest $request, int $id): JsonResponse
    {
        // サービスの実行
        return $this->service->updateAdminData($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  AdminDeleteRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(AdminDeleteRequest $request): JsonResponse
    {
        // サービスの実行
        return $response = $this->service->deleteAdmin($request);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  AdminUpdatePasswordRequest $request
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword(AdminUpdatePasswordRequest $request, int $id): JsonResponse
    {
        // サービスの実行
        return $this->service->updateAdminPassword($id, $request->currentPassword, $request->newPassword);
    }

    /**
     * forgot password recover request.
     *
     * @param  AdminForgotPasswordRequest $request
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(AdminForgotPasswordRequest $request): JsonResponse
    {
        // サービスの実行
        return $this->service->forgotAdminPassword($request->email);
    }

    /**
     * reset password request.
     *
     * @param  AdminResetPasswordRequest $request
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(AdminResetPasswordRequest $request): JsonResponse
    {
        // セッションIDの取得
        $sessionId = $this->getPasswordResetSessionId($request);

        // サービスの実行
        return $this->service->resetAdminPassword($sessionId, $request->password, $request->token);
    }
}
