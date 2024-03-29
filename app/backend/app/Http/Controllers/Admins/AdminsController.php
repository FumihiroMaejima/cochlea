<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
// use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Library\Response\ResponseLibrary;
use App\Services\Admins\AdminsService;
use App\Http\Requests\Admin\Admins\AdminCreateRequest;
use App\Http\Requests\Admin\Admins\AdminDeleteRequest;
use App\Http\Requests\Admin\Admins\AdminUpdateRequest;
use App\Http\Requests\Admin\Admins\AdminUpdatePasswordRequest;
use App\Http\Requests\Admin\Admins\AdminForgotPasswordRequest;
use App\Http\Requests\Admin\Admins\AdminResetPasswordRequest;
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
        $this->middleware('customAuth:api-admins', ['except' => ['forgotPassword', 'resetPassword']]);
        $this->service = $adminsService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws MyApplicationHttpException
     */
    public function index(Request $request): JsonResponse
    {
        // 権限チェック
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.admins'))) {
            throw new MyApplicationHttpException(StatusCodeMessages::STATUS_403);
        }

        return ResponseLibrary::jsonResponse($this->service->getAdmins($request));
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws MyApplicationHttpException
     */
    public function download(Request $request): BinaryFileResponse
    {
        // 権限チェック
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.admins'))) {
            throw new MyApplicationHttpException(StatusCodeMessages::STATUS_403);
        }

        // サービスの実行
        return $this->service->downloadCSV($request);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  AdminCreateRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws MyApplicationHttpException
     */
    public function create(AdminCreateRequest $request): JsonResponse
    {
        $this->service->createAdmin($request);
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
     * @throws MyApplicationHttpException
     */
    public function update(AdminUpdateRequest $request, int $id): JsonResponse
    {
        // サービスの実行
        $this->service->updateAdmin($request, $id);
        return ResponseLibrary::jsonResponse();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  AdminDeleteRequest  $request
     * @return \Illuminate\Http\JsonResponse
     * @throws MyApplicationHttpException
     */
    public function destroy(AdminDeleteRequest $request): JsonResponse
    {
        // サービスの実行
        $this->service->deleteAdmin($request);
        return ResponseLibrary::jsonResponse();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  AdminUpdatePasswordRequest $request
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws MyApplicationHttpException
     */
    public function updatePassword(AdminUpdatePasswordRequest $request, int $id): JsonResponse
    {
        // サービスの実行
        $this->service->updateAdminPassword($id, $request->currentPassword, $request->newPassword);
        return ResponseLibrary::jsonResponse();
    }

    /**
     * forgot password recover request.
     *
     * @param  AdminForgotPasswordRequest $request
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws MyApplicationHttpException
     */
    public function forgotPassword(AdminForgotPasswordRequest $request): JsonResponse
    {
        // サービスの実行
        $this->service->forgotAdminPassword($request->email);
        return ResponseLibrary::jsonResponse();
    }

    /**
     * reset password request.
     *
     * @param  AdminResetPasswordRequest $request
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws MyApplicationHttpException
     */
    public function resetPassword(AdminResetPasswordRequest $request): JsonResponse
    {
        // セッションIDの取得
        $sessionId = $this->getPasswordResetSessionId($request);

        // サービスの実行
        $this->service->resetAdminPassword($sessionId, $request->password, $request->token);
        return ResponseLibrary::jsonResponse();
    }
}
