<?php

declare(strict_types=1);

namespace App\Services\Admins;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Exports\Admins\AdminsExport;
use App\Repositories\Masters\AdminsRoles\AdminsRolesRepositoryInterface;
use App\Repositories\Masters\AdminsRepositoryInterface;
use App\Http\Requests\Admin\Admins\AdminCreateRequest;
use App\Http\Requests\Admin\Admins\AdminDeleteRequest;
use App\Http\Requests\Admin\Admins\AdminUpdateRequest;
use App\Http\Resources\Admins\AdminsCollection;
use App\Http\Resources\Admins\AdminsResource;
use App\Http\Resources\Admins\AdminsRolesResource;
use App\Http\Resources\Admins\AdminUpdateNotificationResource;
use App\Library\Array\ArrayLibrary;
use App\Library\Time\TimeLibrary;
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
     * @param \App\Repositories\Masters\AdminsRepositoryInterface $adminsRepository
     * @param \App\Repositories\Masters\AdminsRoles\AdminsRolesRepositoryInterface $adminsRepository
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
     * @return array
     */
    public function getAdmins(Request $request): array
    {
        $admins = $this->adminsRepository->getAdminsList();
        // サービスコンテナからリソースクラスインスタンスを依存解決
        // コンストラクタのresourceに割り当てる値を渡す
        // $resourceCollection = app()->make(AdminsCollection::class, ['resource' => $admins]);
        $resourceCollection = new AdminsCollection($admins);
        // $resourceCollection = app()->make(AdminsResource::class, ['resource' => $admins]);
        // $resource = app()->make(AdminsResource::class, ['resource' => $data]);

        // dataキーに格納されている
        return $resourceCollection->toArray($request);
        // return response()->json($resourceCollection->toArray($request), 200);
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

        return Excel::download(new AdminsExport($data), 'admins_list_' . TimeLibrary::getCurrentDateTime(TimeLibrary::DATE_TIME_FORMAT_YMDHIS) . '.csv');
    }

    /**
     * creata admin data service
     *
     * @param AdminCreateRequest $request
     * @param int $id
     * @return void
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws MyApplicationHttpException
     */
    public function createAdmin(AdminCreateRequest $request): void
    {
        DB::beginTransaction();
        try {
            $resource = AdminsResource::toArrayForCreate($request);

            $insertAdminResult = $this->adminsRepository->create($resource); // if created => count is 1
            $latestAdmin = $this->adminsRepository->getLatestAdmin();
            $latestAdmin = ArrayLibrary::toArray(ArrayLibrary::getFirst($latestAdmin->toArray()));

            // 権限情報の作成
            $adminsRolesResource = AdminsRolesResource::toArrayForCreate($request, $latestAdmin);
            $insertAdminsRolesResult = $this->adminsRolesRepository->create($adminsRolesResource);

            // 作成出来ない場合
            if (!($insertAdminResult && $insertAdminsRolesResult)) {
                throw new MyApplicationHttpException(
                    StatusCodeMessages::STATUS_401,
                    parameter: [
                        'resource' => $resource,
                        'adminsRolesResource' => $adminsRolesResource,
                    ]
                );
            }

            DB::commit();

            return;
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'message: ' . json_encode($e->getMessage()));
            DB::rollback();

            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_500,
                $e->getMessage()
            );
            // abort(500);
        }
    }

    /**
     * update admin data service
     *
     * @param AdminUpdateRequest $request
     * @param int $id
     * @return void
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws MyApplicationHttpException
     */
    public function updateAdmin(AdminUpdateRequest $request, int $id): void
    {
        DB::beginTransaction();
        try {
            // ロックをかける為transaction内で実行
            $admin = $this->getAdminById($id);

            $resource = AdminsResource::toArrayForUpdate($request);

            $updatedRowCount = $this->adminsRepository->update($admin[Admins::ID], $resource);

            // 権限情報の更新
            $roleIdResource = AdminsRolesResource::toArrayForUpdate($request);
            $updatedAdminsRolesRowCount = $this->adminsRolesRepository->update($id, $roleIdResource);

            // 更新出来ない場合
            // 更新されていない場合は304を返すでも良さそう
            if (!($updatedRowCount && $updatedAdminsRolesRowCount)) {
                throw new MyApplicationHttpException(
                    StatusCodeMessages::STATUS_401,
                    parameter: [
                        'resource' => $resource,
                        'roleIdResource' => $roleIdResource,
                    ]
                );
            }

            // slack通知
            $attachmentResource = app()->make(AdminUpdateNotificationResource::class, ['resource' => ":tada: Update Member Data \n"])->toArray($request);
            app()->make(AdminsSlackNotificationService::class)->send('update admin data.', $attachmentResource);

            DB::commit();

            return;
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'message: ' . json_encode($e->getMessage()));
            DB::rollback();

            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_500,
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
            // ロックをかける為transaction内で実行
            $admin = $this->getAdminById((int)$request->id);

            $resource = AdminsResource::toArrayForDelete();

            $deleteRowCount = $this->adminsRepository->delete($admin[Admins::ID], $resource);

            // 権限情報の更新
            $roleIdResource = AdminsRolesResource::toArrayForDelete($request);
            $deleteAdminsRolesRowCount = $this->adminsRolesRepository->delete($admin[Admins::ID], $roleIdResource);

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
                StatusCodeMessages::STATUS_500,
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
        DB::beginTransaction();
        try {
            // ロックをかける為transaction内で実行
            $admin = $this->getAdminById($id);

            // 現在のパスワードのチェック
            if (!Hash::check($currentPassword, $admin[Admins::PASSWORD])) {
                throw new MyApplicationHttpException(
                    StatusCodeMessages::STATUS_404,
                    'hash check failed.'
                );
            }

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
     * forgot password service
     *
     * @param string $email mail address
     * @return \Illuminate\Http\JsonResponse
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function forgotAdminPassword(string $email): JsonResponse
    {
        try {
            // ロックをかける為transaction内で実行
            $admin = $this->getAdminByEmail($email);

            $sessionId = RandomStringLibrary::getRandomShuffleString(self::PASSWORD_RESET_TOKEN_LENGTH);
            $token = RandomStringLibrary::getRandomShuffleString(self::PASSWORD_RESET_TOKEN_LENGTH);

            // キャッシュに保存
            CacheLibrary::setCache(
                self::CACHE_KEY_PREFIX_ADMIN_PASSWORD_RESET_SESSION . $sessionId,
                $token . '_' . $admin[Admins::ID],
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
     * reset password service
     *
     * @param string $sessionId session id
     * @param string $password password
     * @param string $token reset password token
     * @return \Illuminate\Http\JsonResponse
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function resetAdminPassword(string $sessionId, string $password, $token): JsonResponse
    {
        $cache = CacheLibrary::getByKey(self::CACHE_KEY_PREFIX_ADMIN_PASSWORD_RESET_SESSION . $sessionId);

        if (empty($cache)) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_401,
                'failed password update. maybe session expired.'
            );
        }

        // adminIdとトークンの配列化
        $session = explode('_', $cache);

        $admin = $this->getAdminById((int)$session[1]);

        // token check
        if ($token !== $session[0]) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_500,
                'failed password update.'
            );
        }

        DB::beginTransaction();
        try {
            $resource = AdminsResource::toArrayForUpdatePassword($password);

            $updatedRowCount = $this->adminsRepository->updatePassword($admin[Admins::ID], $resource);

            DB::commit();

            // キャッシュの削除
            CacheLibrary::deleteCache(self::CACHE_KEY_PREFIX_ADMIN_PASSWORD_RESET_SESSION . $sessionId);

            // 更新されていない場合は304
            $message = ($updatedRowCount > 0) ? 'success' : 'not modified';
            $status = ($updatedRowCount > 0) ? 200 : 304;

            return response()->json(['message' => $message, 'status' => $status], $status);
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'message: ' . json_encode($e->getMessage()));
            DB::rollback();

            throw $e;
        }
    }

    /**
     * get admin by admin id.
     *
     * @param int $adminId admin id
     * @return array
     */
    private function getAdminById(int $adminId): array
    {
        // 更新用途で使う為lockをかける
        $admins = $this->adminsRepository->getById($adminId, true);

        if (empty($admins)) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_500,
                'not exist admin.'
            );
        }

        // 複数チェックはrepository側で実施済み
        return ArrayLibrary::toArray(ArrayLibrary::getFirst($admins->toArray()));
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
                StatusCodeMessages::STATUS_500,
                'not exist admin.'
            );
        }

        // 複数チェックはrepository側で実施済み
        return ArrayLibrary::toArray(ArrayLibrary::getFirst($admins->toArray()));
    }
}
