<?php

declare(strict_types=1);

namespace App\Services\Admins;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Exports\Admins\RolesExport;
use App\Http\Requests\Admin\Roles\RoleCreateRequest;
use App\Http\Requests\Admin\Roles\RoleDeleteRequest;
use App\Http\Requests\Admin\Roles\RoleUpdateRequest;
use App\Http\Resources\Admins\RolePermissionsResource;
use App\Http\Resources\Admins\RolesResource;
use App\Http\Resources\Admins\RoleUpdateNotificationResource;
use App\Library\Array\ArrayLibrary;
use App\Library\Cache\CacheLibrary;
use App\Library\Time\TimeLibrary;
use App\Models\Masters\Roles;
use App\Repositories\Masters\RolePermissions\RolePermissionsRepositoryInterface;
use App\Repositories\Masters\Roles\RolesRepositoryInterface;
use App\Services\Admins\Notifications\RoleSlackNotificationService;
use Exception;

class RolesService
{
    // cache keys
    private const CACHE_KEY_ADMIN_ROLE_COLLECTION_LIST = 'admin_role_collection_list';

    protected RolesRepositoryInterface $rolesRepository;
    protected RolePermissionsRepositoryInterface $rolePermissionsRepository;

    /**
     * create RolesService instance
     * @param  \App\Repositories\Roles\RolesRepositoryInterface  $rolesRepository
     * @param  \App\Repositories\RolePermissions\RolePermissionsRepositoryInterface  $rolePermissionsRepository
     * @return void
     */
    public function __construct(RolesRepositoryInterface $rolesRepository, RolePermissionsRepositoryInterface $rolePermissionsRepository)
    {
        $this->rolesRepository = $rolesRepository;
        $this->rolePermissionsRepository = $rolePermissionsRepository;
    }

    /**
     * get roles data
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function getRoles(Request $request): array
    {
        $cache = CacheLibrary::getByKey(self::CACHE_KEY_ADMIN_ROLE_COLLECTION_LIST);

        // キャッシュチェック
        if (is_null($cache)) {
            $collection = $this->rolesRepository->getRoles();
            // $resourceCollection = app()->make(RolesServiceResource::class, ['resource' => $collection]);
            // $resourceCollection->toArray($request)
            $resourceCollection = RolesResource::toArrayForGetRolesCollection($collection);

            if (!empty($resourceCollection)) {
                CacheLibrary::setCache(self::CACHE_KEY_ADMIN_ROLE_COLLECTION_LIST, $resourceCollection);
            }
        } else {
            $resourceCollection = (array)$cache;
        }

        // TODO GitHub ActionsのUnitテストが成功したら削除
        /* $collection = $this->rolesRepository->getRoles();
        // $resourceCollection = app()->make(RolesServiceResource::class, ['resource' => $collection]);
        // $resourceCollection->toArray($request)
        $resourceCollection = RolesResource::toArrayForGetRolesCollection($collection); */

        return $resourceCollection;
    }

    /**
     * get roles data for frontend parts
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function getRolesList(Request $request): array
    {
        $data = $this->rolesRepository->getRolesList();
        // サービスコンテナからリソースクラスインスタンスを依存解決
        // コンストラクタのresourceに割り当てる値を渡す
        // $resource = app()->make(RolesListResource::class, ['resource' => $data]);
        // $resource->toArray($request);
        $resource = RolesResource::toArrayForGetTextAndValueList($data);

        return $resource;
    }

    /**
     * download role data service
     *
     * @param  \Illuminate\Http\Request;  $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadCSV(Request $request)
    {
        $data = $this->rolesRepository->getRoles();

        return Excel::download(new RolesExport($data), 'roles_info_' . TimeLibrary::getCurrentDateTime(TimeLibrary::DATE_TIME_FORMAT_YMDHIS) . '.csv');
    }

    /**
     * update role data service
     *
     * @param  RoleCreateRequest  $request
     * @param  int  $id
     * @return void
     */
    public function createRole(RoleCreateRequest $request): void
    {
        DB::beginTransaction();
        try {
            $resource = RolesResource::toArrayForCreate($request);


            $insertResult = $this->rolesRepository->create($resource); // if created => count is 1
            $latestRoles = $this->rolesRepository->getLatestRole();

            // 権限情報の作成
            $permissonsResource = RolePermissionsResource::toArrayForCreate($request, $latestRoles);

            $insertRolePermissionsResult = $this->rolePermissionsRepository->create($permissonsResource);

            // 作成出来ない場合
            if (!($insertResult && $insertRolePermissionsResult)) {
                throw new MyApplicationHttpException(
                    StatusCodeMessages::STATUS_401,
                    parameter: [
                        'resource' => $resource,
                        'permissonsResource' => $permissonsResource,
                    ]
                );
            }

            DB::commit();

            // キャッシュの削除
            CacheLibrary::deleteCache(self::CACHE_KEY_ADMIN_ROLE_COLLECTION_LIST, true);

            // return response()->json(['message' => $message, 'status' => $status], $status);
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'message: ' . json_encode($e->getMessage()));
            DB::rollback();
            abort(500);
        }
    }

    /**
     * update role data service
     *
     * @param int $id role id
     * @param RoleUpdateRequest $request
     * @return void
     */
    public function updateRole(int $id, RoleUpdateRequest $request): void
    {
        DB::beginTransaction();
        try {
            // ロックをかける為transaction内で実行
            $role = $this->getRoleById($id);
            $resource = RolesResource::toArrayForUpdate($request);

            $updatedRowCount = $this->rolesRepository->update($role[Roles::ID], $resource);

            // 権限情報の更新
            $removeResource = RolePermissionsResource::toArrayForDeleteByUpdateResource($request);

            $this->rolePermissionsRepository->delete($role[Roles::ID], $removeResource);

            $updateResource = RolePermissionsResource::toArrayForUpdate($request);
            $updatedRolePermissionsRowCount = $this->rolePermissionsRepository->create($updateResource);


            // 更新出来ない場合
            // 更新されていない場合は304を返すでも良さそう
            if (!($updatedRowCount > 0 || $updatedRolePermissionsRowCount > 0)) {
                throw new MyApplicationHttpException(
                    StatusCodeMessages::STATUS_401,
                    parameter: [
                        'resource' => $resource,
                        'removeResource' => $removeResource,
                        'updateResource' => $updateResource,
                    ]
                );
            }

            // slack通知
            $attachmentResource = app()->make(RoleUpdateNotificationResource::class, ['resource' => ":tada: Update Role Data \n"])->toArray($request);
            app()->make(RoleSlackNotificationService::class)->send('update role data.', $attachmentResource);

            DB::commit();

            // キャッシュの削除
            CacheLibrary::deleteCache(self::CACHE_KEY_ADMIN_ROLE_COLLECTION_LIST, true);

            // return response()->json(['message' => $message, 'status' => $status], $status);
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'message: ' . json_encode($e->getMessage()));
            DB::rollback();
            abort(500);
        }
    }

    /**
     * delete role data service
     *
     * @param RoleDeleteRequest $request
     * @param int $id role id
     * @return void
     */
    public function deleteRole(RoleDeleteRequest $request): void
    {
        DB::beginTransaction();
        try {
            $roleIds = $request->roles;

            $resource = RolesResource::toArrayForDelete();

            // ロックをかける為transaction内で実行
            $this->getRolesByIds($roleIds);

            $deleteRowCount = $this->rolesRepository->deleteByIds($roleIds, $resource);

            // 権限情報の更新
            $rolePermissionsResource = RolePermissionsResource::toArrayForDelete();
            $deleteRolePermissionsRowCount = $this->rolePermissionsRepository->deleteByIds($roleIds, $rolePermissionsResource);

            // 更新出来ない場合
            if (!($deleteRowCount > 0 && $deleteRolePermissionsRowCount > 0)) {
                throw new MyApplicationHttpException(
                    StatusCodeMessages::STATUS_401,
                    parameter: [
                        'resource' => $resource,
                        'rolePermissionsResource' => $rolePermissionsResource,
                    ]
                );
            }

            DB::commit();

            // キャッシュの削除
            CacheLibrary::deleteCache(self::CACHE_KEY_ADMIN_ROLE_COLLECTION_LIST, true);

            // return response()->json(['message' => $message, 'status' => $status], $status);
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'message: ' . json_encode($e->getMessage()));
            DB::rollback();
            abort(500);
        }
    }

    /**
     * get role by role id.
     *
     * @param int $roleId role id
     * @return array
     */
    private function getRoleById(int $roleId): array
    {
        // 更新用途で使う為lockをかける
        $roles = $this->rolesRepository->getById($roleId, true);

        if (empty($roles)) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_500,
                'not exist role.'
            );
        }

        // 複数チェックはrepository側で実施済み
        return ArrayLibrary::toArray(ArrayLibrary::getFirst($roles->toArray()));
    }

    /**
     * get roles by role ids.
     *
     * @param array $roleIds role id
     * @return array
     */
    private function getRolesByIds(array $roleIds): array
    {
        // 更新用途で使う為lockをかける
        $roles = $this->rolesRepository->getByIds($roleIds, true);

        if (empty($roles)) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_500,
                'not exist roles.'
            );
        }

        // 複数チェックはrepository側で実施済み
        return ArrayLibrary::toArray($roles->toArray());
    }
}
