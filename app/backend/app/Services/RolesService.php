<?php

namespace App\Services;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\Notifications\RoleSlackNotificationService;
use App\Repositories\Admins\Roles\RolesRepositoryInterface;
use App\Repositories\Admins\RolePermissions\RolePermissionsRepositoryInterface;
use App\Http\Resources\Admins\RolesResource;
use App\Http\Resources\Admins\RoleUpdateNotificationResource;
use App\Http\Resources\Admins\RolePermissionsResource;
use App\Http\Requests\Admins\RoleUpdateRequest;
use App\Http\Requests\Admins\RoleDeleteRequest;
use App\Http\Requests\Admins\RoleCreateRequest;
use App\Exports\Admins\RolesExport;
use App\Library\Cache\CacheLibrary;
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
     * @return JsonResponse
     */
    public function getRoles(Request $request): JsonResponse
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
            $resourceCollection = $cache;
        }

        // TODO GitHub ActionsのUnitテストが成功したら削除
        /* $collection = $this->rolesRepository->getRoles();
        // $resourceCollection = app()->make(RolesServiceResource::class, ['resource' => $collection]);
        // $resourceCollection->toArray($request)
        $resourceCollection = RolesResource::toArrayForGetRolesCollection($collection); */

        return response()->json($resourceCollection, 200);
    }

    /**
     * get roles data for frontend parts
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    public function getRolesList(Request $request): JsonResponse
    {
        $data = $this->rolesRepository->getRolesList();
        // サービスコンテナからリソースクラスインスタンスを依存解決
        // コンストラクタのresourceに割り当てる値を渡す
        // $resource = app()->make(RolesListResource::class, ['resource' => $data]);
        // $resource->toArray($request);
        $resource = RolesResource::toArrayForGetTextAndValueList($data);

        return response()->json($resource, 200);
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

        return Excel::download(new RolesExport($data), 'roles_info_' . Carbon::now()->format('YmdHis') . '.csv');
    }

    /**
     * update role data service
     *
     * @param  RoleCreateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function createRole(RoleCreateRequest $request)
    {
        DB::beginTransaction();
        try {
            $resource = RolesResource::toArrayForCreate($request);


            $insertCount = $this->rolesRepository->createRole($resource); // if created => count is 1
            $latestRoles = $this->rolesRepository->getLatestRole();

            // 権限情報の作成
            $permissonsResource = RolePermissionsResource::toArrayForCreate($request, $latestRoles);

            $insertRolePermissionsCount = $this->rolePermissionsRepository->createRolePermission($permissonsResource);

            DB::commit();

            // 作成されている場合は304
            $message = ($insertCount > 0 && $insertRolePermissionsCount > 0) ? 'success' : 'Bad Request';
            $status = ($insertCount > 0 && $insertRolePermissionsCount > 0) ? 201 : 401;

            return response()->json(['message' => $message, 'status' => $status], $status);
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'message: ' . json_encode($e->getMessage()));
            DB::rollback();
            abort(500);
        }
    }

    /**
     * update role data service
     *
     * @param  RoleUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateRoleData(RoleUpdateRequest $request, int $id)
    {
        DB::beginTransaction();
        try {
            $resource = RolesResource::toArrayForUpdate($request);

            $updatedRowCount = $this->rolesRepository->updateRoleData($resource, $id);

            // 権限情報の更新
            $removeResource = RolePermissionsResource::toArrayForDeleteByUpdateResource($request);

            $this->rolePermissionsRepository->deleteRolePermissionsData($removeResource, $id);

            $updateResource = RolePermissionsResource::toArrayForUpdate($request);
            $updatedRolePermissionsRowCount = $this->rolePermissionsRepository->createRolePermission($updateResource, $id);

            // slack通知
            $attachmentResource = app()->make(RoleUpdateNotificationResource::class, ['resource' => ":tada: Update Role Data \n"])->toArray($request);
            app()->make(RoleSlackNotificationService::class)->send('update role data.', $attachmentResource);

            DB::commit();

            // 更新されていない場合は304
            $message = ($updatedRowCount > 0 || $updatedRolePermissionsRowCount > 0) ? 'success' : 'not modified';
            $status = ($updatedRowCount > 0 || $updatedRolePermissionsRowCount > 0) ? 200 : 304;

            return response()->json(['message' => $message, 'status' => $status], $status);
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'message: ' . json_encode($e->getMessage()));
            DB::rollback();
            abort(500);
        }
    }

    /**
     * delete role data service
     *
     * @param  RoleDeleteRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteRole(RoleDeleteRequest $request)
    {
        DB::beginTransaction();
        try {
            $roleIds = $request->roles;

            $resource = RolesResource::toArrayForDelete();

            $deleteRowCount = $this->rolesRepository->deleteRoleData($resource, $roleIds);

            // 権限情報の更新
            $rolePermissionsResource = RolePermissionsResource::toArrayForDelete();
            $deleteRolePermissionsRowCount = $this->rolePermissionsRepository->deleteRolePermissionsByIds($rolePermissionsResource, $roleIds);

            DB::commit();

            // 更新されていない場合は304
            $message = ($deleteRowCount > 0 && $deleteRolePermissionsRowCount > 0) ? 'success' : 'not deleted';
            $status = ($deleteRowCount > 0 && $deleteRolePermissionsRowCount > 0) ? 200 : 401;

            return response()->json(['message' => $message, 'status' => $status], $status);
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'message: ' . json_encode($e->getMessage()));
            DB::rollback();
            abort(500);
        }
    }
}
