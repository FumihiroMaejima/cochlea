<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exceptions\MyApplicationHttpException;
use App\Exceptions\ExceptionStatusCodeMessages;
use App\Exports\Admins\AdminsExport;
use App\Repositories\Admins\AdminsRoles\AdminsRolesRepositoryInterface;
use App\Repositories\Admins\AdminsRepositoryInterface;
use App\Http\Requests\Admins\AdminCreateRequest;
use App\Http\Requests\Admins\AdminDeleteRequest;
use App\Http\Requests\Admins\AdminUpdateRequest;
use App\Http\Resources\Admins\AdminCreateResource;
use App\Http\Resources\Admins\AdminDeleteResource;
use App\Http\Resources\Admins\AdminsCollection;
use App\Http\Resources\Admins\AdminsRolesCreateResource;
use App\Http\Resources\Admins\AdminsRolesDeleteResource;
use App\Http\Resources\Admins\AdminsRolesUpdateResource;
use App\Http\Resources\Admins\AdminsResource;
use App\Http\Resources\Admins\AdminUpdateResource;
use App\Http\Resources\Admins\AdminUpdateNotificationResource;
use App\Services\Notifications\AdminsSlackNotificationService;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Exception;

class AdminsService
{
    protected AdminsRepositoryInterface $adminsRepository;
    protected AdminsRolesRepositoryInterface $adminsRolesRepository;

    /**
     * create AdminsService instance
     * @param \App\Repositories\Admins\AdminsRepositoryInterface $adminsRepository
     * @param \App\Repositories\Admins\AdminsRoles\AdminsRolesRepositoryInterface $adminsRepository
     * @return void
     */
    public function __construct(AdminsRepositoryInterface $adminsRepository, AdminsRolesRepositoryInterface $adminsRolesRepository)
    {
        $this->adminsRepository = $adminsRepository;
        $this->adminsRolesRepository = $adminsRolesRepository;
    }

    /**
     * get admins data
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAdmins(Request $request): JsonResponse
    {
        $admins = $this->adminsRepository->getAdminsList();
        // サービスコンテナからリソースクラスインスタンスを依存解決
        // コンストラクタのresourceに割り当てる値を渡す
        $resourceCollection = app()->make(AdminsCollection::class, ['resource' => $admins]);
        // $resourceCollection = app()->make(AdminsResource::class, ['resource' => $admins]);
        // $resource = app()->make(AdminsResource::class, ['resource' => $data]);

        return response()->json($resourceCollection->toArray($request), 200);
        // return response()->json($resource->toArray($request), 200);
    }

    /**
     * download admin data by csv
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadCSV(Request $request): BinaryFileResponse
    {
        $data = $this->adminsRepository->getAdminsList();

        return Excel::download(new AdminsExport($data), 'admins_list_' . Carbon::now()->format('YmdHis') . '.csv');
    }

    /**
     * creata admin data service
     *
     * @param AdminCreateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function createAdmin(AdminCreateRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $resource = app()->make(AdminCreateResource::class, ['resource' => $request])->toArray($request);

            $insertCount = $this->adminsRepository->createAdmin($resource); // if created => count is 1
            $latestAdmin = $this->adminsRepository->getLatestAdmin();

            // 権限情報の作成
            $adminsRolesResource = app()->make(AdminsRolesCreateResource::class, ['resource' => $latestAdmin])->toArray($request);
            $insertAdminsRolesCount = $this->adminsRolesRepository->createAdminsRole($adminsRolesResource);

            DB::commit();

            // 作成されている場合は304
            $message = ($insertCount > 0 && $insertAdminsRolesCount > 0) ? 'success' : 'Bad Request';
            $status = ($insertCount > 0 && $insertAdminsRolesCount > 0) ? 201 : 401;

            return response()->json(['message' => $message, 'status' => $status], $status);
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'message: ' . json_encode($e->getMessage()));
            DB::rollback();

            throw new MyApplicationHttpException(
                ExceptionStatusCodeMessages::STATUS_CODE_500,
                $e->getMessage()
            );
            // abort(500);
        }
    }

    /**
     * update admin data service
     *
     * @param \App\Http\Requests\AdminUpdateRequest $request
     * @param int  $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function updateAdminData(AdminUpdateRequest $request, int $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $resource = app()->make(AdminUpdateResource::class, ['resource' => $request])->toArray($request);

            $updatedRowCount = $this->adminsRepository->updateAdminData($resource, $id);

            // 権限情報の更新
            $roleIdResource = app()->make(AdminsRolesUpdateResource::class, ['resource' => $request])->toArray($request);
            $updatedAdminsRolesRowCount = $this->adminsRolesRepository->updateAdminsRoleData($roleIdResource, $id);

            // slack通知
            $attachmentResource = app()->make(AdminUpdateNotificationResource::class, ['resource' => ":tada: Update Member Data \n"])->toArray($request);
            app()->make(AdminsSlackNotificationService::class)->send('update admin data.', $attachmentResource);

            DB::commit();

            // 更新されていない場合は304
            $message = ($updatedRowCount > 0 || $updatedAdminsRolesRowCount > 0) ? 'success' : 'not modified';
            $status = ($updatedRowCount > 0 || $updatedAdminsRolesRowCount > 0) ? 200 : 304;

            return response()->json(['message' => $message, 'status' => $status], $status);
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'message: ' . json_encode($e->getMessage()));
            DB::rollback();

            throw new MyApplicationHttpException(
                ExceptionStatusCodeMessages::STATUS_CODE_500,
                $e->getMessage()
            );
            // abort(500);
        }
    }

    /**
     * delete admin data service
     *
     * @param AdminDeleteRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function deleteAdmin(AdminDeleteRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $id = $request->id;

            $resource = app()->make(AdminDeleteResource::class, ['resource' => $request])->toArray($request);

            $deleteRowCount = $this->adminsRepository->deleteAdminData($resource, $request->id);

            // 権限情報の更新
            $roleIdResource = app()->make(AdminsRolesDeleteResource::class, ['resource' => $request])->toArray($request);
            $deleteAdminsRolesRowCount = $this->adminsRolesRepository->deleteAdminsRoleData($roleIdResource, $id);

            DB::commit();

            // 更新されていない場合は304
            $message = ($deleteRowCount > 0 && $deleteAdminsRolesRowCount > 0) ? 'success' : 'not deleted';
            $status = ($deleteRowCount > 0 && $deleteAdminsRolesRowCount > 0) ? 200 : 401;

            return response()->json(['message' => $message, 'status' => $status], $status);
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'message: ' . json_encode($e->getMessage()));
            DB::rollback();

            // $e->getTraceAsString();
            throw new MyApplicationHttpException(
                ExceptionStatusCodeMessages::STATUS_CODE_500,
                $e->getMessage(),
            );
            // abort(500);
        }
    }
}
