<?php

namespace App\Repositories\Admins\RolePermissions;

use App\Models\Roles;
use App\Models\Permissions;
use App\Models\RolePermissions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class RolePermissionsRepository implements RolePermissionsRepositoryInterface
{
    protected $model;
    protected $permissionsModel;
    protected $rolesModel;

    /**
     * Create a new AuthInfoController instance.
     *
     * @return void
     */
    public function __construct(RolePermissions $model, Permissions $permissionsModel, Roles $rolesModel)
    {
        $this->model            = $model;
        $this->permissionsModel = $permissionsModel;
        $this->rolesModel       = $rolesModel;
    }

    /**
     * Get Model Table Name in This Repository.
     *
     * @return Collection
     */
    public function getTable(): string
    {
        return $this->model->getTable();
    }

    /**
     * Get All Role Data.
     * @param int $roleId
     * @return Collection
     */
    public function getByRoleId(int $roleId): Collection
    {
        $rolePermissions = $this->model->getTable();
        $roles = $this->rolesModel->getTable();

        return DB::table($rolePermissions)
            ->select([$rolePermissions . '.id', $rolePermissions . '.role_id', $rolePermissions . '.permission_id', $roles .'.name', $roles . '.code'])
            ->leftJoin($roles, $rolePermissions . '.role_id', '=', $roles . '.id')
            ->where('role_id', '=', [$roleId])
            ->where($rolePermissions . '.deleted_at', '=', null)
            ->get();
    }
    /**
     * create Admin data.
     * @param array $resource
     * @return int
     */
    public function createRolePermission(array $resource): int
    {
        return DB::table($this->model->getTable())->insert($resource);
    }

    /**
     * update Role data.
     * @param array $resource
     * @param int $roleId
     * @return int
     */
    public function updateRolePermissionsData(array $resource, int $roleId): int
    {
        // role_permissions
        $rolePermissions = $this->model->getTable();

        // Query Builderのupdate
        return DB::table($rolePermissions)
            // ->whereIn('id', [$id])
            ->where('role_id', '=', [$roleId])
            ->where('deleted_at', '=', null)
            ->update($resource);
    }

    /**
     * delete Role data.
     * @param array $resource
     * @param int $id
     * @return int
     */
    public function deleteRolePermissionsData(array $resource, int $roleId): int
    {
        // role_permissions
        $rolePermissions = $this->model->getTable();

        // Query Builderのupdate
        return DB::table($rolePermissions)
            // ->whereIn('id', [$id])
            ->where('role_id', '=', $roleId)
            ->where('deleted_at', '=', null)
            ->update($resource);
    }

    /**
     * delete Role data by array data.
     * @param array $resource
     * @param array $ids
     * @return int
     */
    public function deleteRolePermissionsByIds(array $resource, array $ids): int
    {
        // role_permissions
        $rolePermissions = $this->model->getTable();

        return DB::table($rolePermissions)
            ->whereIn('role_id', $ids)
            ->where('deleted_at', '=', null)
            ->update($resource);
    }
}
