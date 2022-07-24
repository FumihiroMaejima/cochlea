<?php

namespace App\Services\Admins;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
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
use App\Http\Resources\Admins\AdminsCollection;
use App\Http\Resources\Admins\AdminsResource;
use App\Http\Resources\Admins\AdminsRolesResource;
use App\Http\Resources\Admins\AdminUpdateNotificationResource;
use App\Library\Array\ArrayLibrary;
use App\Models\Masters\Admins;
use App\Services\Admins\Notifications\AdminsSlackNotificationService;
use App\Services\Admins\Notifications\PasswordForgotNotificationService;
use App\Library\Cache\CacheLibrary;
use App\Library\Random\RandomStringLibrary;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Exception;

class AdminsService
{
    // cache keys
    private const CACHE_KEY_PREFIX_ADMIN_PASSWORD_RESET_SESSION = 'admin_password_reset_session_id_';

    private const PASSWORD_RESET_TOKEN_LENGTH = 20;
    private const PASSWORD_RESET_SESSION_EXPIRE = 900; // パスワードリセット有効期限(900秒=15分)

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
        // $resourceCollection = app()->make(AdminsCollection::class, ['resource' => $admins]);
        $resourceCollection = new AdminsCollection($admins);
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
            $resource = AdminsResource::toArrayForCreate($request);

            $insertCount = $this->adminsRepository->createAdmin($resource); // if created => count is 1
            $latestAdmin = $this->adminsRepository->getLatestAdmin();

            // 権限情報の作成
            $adminsRolesResource = AdminsRolesResource::toArrayForCreate($request, $latestAdmin);
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
     * @param AdminUpdateRequest $request
     * @param int  $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function updateAdminData(AdminUpdateRequest $request, int $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $resource = AdminsResource::toArrayForUpdate($request);

            $updatedRowCount = $this->adminsRepository->updateAdminData($resource, $id);

            // 権限情報の更新
            $roleIdResource = AdminsRolesResource::toArrayForUpdate($request);
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

            $resource = AdminsResource::toArrayForDelete();

            $deleteRowCount = $this->adminsRepository->deleteAdminData($resource, $request->id);

            // 権限情報の更新
            $roleIdResource = AdminsRolesResource::toArrayForDelete($request);
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

    /**
     * update admin data service
     *
     * @param int $id admin id
     * @param string $currentPassword current password
     * @param string $newPassword new password
     * @return \Illuminate\Http\JsonResponse
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function updateAdminPassword(int $id, string $currentPassword, string $newPassword): JsonResponse
    {
        $admin = $this->getAdminById($id);

        // 現在のパスワードのチェック
        if (!Hash::check($currentPassword, $admin[Admins::PASSWORD])) {
            throw new MyApplicationHttpException(
                ExceptionStatusCodeMessages::STATUS_CODE_404,
                'hash check failed.'
            );
        }

        DB::beginTransaction();
        try {
            $resource = AdminsResource::toArrayForUpdatePassword($newPassword);

            $updatedRowCount = $this->adminsRepository->updatePassword($id, $resource);

            // slack通知
            $attachmentResource = AdminUpdateNotificationResource::toArrayForCreate($admin[Admins::ID], $admin[Admins::NAME], ":tada: Update Admin Password \n");
            app()->make(AdminsSlackNotificationService::class)->send('update admin password.', $attachmentResource);

            DB::commit();

            // 更新されていない場合は304
            $message = ($updatedRowCount > 0) ? 'success' : 'not modified';
            $status = ($updatedRowCount > 0) ? 200 : 304;

            return response()->json(['message' => $message, 'status' => $status], $status);
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'message: ' . json_encode($e->getMessage()));
            DB::rollback();

            throw $e;
            // abort(500);
        }
    }

    /**
     * update admin data service
     *
     * @param string $email mail address
     * @return \Illuminate\Http\JsonResponse
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function forgotAdminPassword(string $email): JsonResponse
    {
        $admin = $this->getAdminByEmail($email);

        try {
            $token = RandomStringLibrary::getRandomShuffleString(self::PASSWORD_RESET_TOKEN_LENGTH);

            // キャッシュに保存
            CacheLibrary::setCache(
                self::CACHE_KEY_PREFIX_ADMIN_PASSWORD_RESET_SESSION.$admin[Admins::ID],
                $token,
                self::PASSWORD_RESET_SESSION_EXPIRE
            );

            // メール送信
            (new PasswordForgotNotificationService($admin[Admins::EMAIL]))->send($token);

            $message = 'success';
            $status = 200;

            return response()->json(['message' => $message, 'status' => $status], $status);
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'message: ' . json_encode($e->getMessage()));

            throw $e;
        }
    }

    /**
     * get admin by admin id.
     *
     * @param int $adminId admin id
     * @return array|null
     */
    private function getAdminById(int $adminId): array|null
    {
        $admins = $this->adminsRepository->getById($adminId);

        if (empty($admins)) {
            throw new MyApplicationHttpException(
                ExceptionStatusCodeMessages::STATUS_CODE_500,
                'not exist admin.'
            );
        }

        // 複数チェックはrepository側で実施済み
        return ArrayLibrary::toArray($admins->toArray()[0]);
    }

    /**
     * get admin by mail address.
     *
     * @param string $email mail address
     * @return array|null
     */
    private function getAdminByEmail(string $email): array|null
    {
        $admins = $this->adminsRepository->getByEmail($email);

        if (empty($admins)) {
            throw new MyApplicationHttpException(
                ExceptionStatusCodeMessages::STATUS_CODE_500,
                'not exist admin.'
            );
        }

        // 複数チェックはrepository側で実施済み
        return ArrayLibrary::toArray($admins->toArray()[0]);
    }
}
