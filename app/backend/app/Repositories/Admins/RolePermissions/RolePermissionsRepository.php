<?php

namespace App\Repositories\Admins\RolePermissions;

use App\Models\Masters\Roles;
use App\Models\Masters\Permissions;
use App\Models\Masters\RolePermissions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class RolePermissionsRepository implements RolePermissionsRepositoryInterface
{
    protected RolePermissions $model;
    protected Permissions $permissionsModel;
    protected Roles $rolesModel;

    /**
     * create a new AuthInfoController instance.
     *
     * @param RolePermissions $model
     * @param Permissions $permissionsModel
     * @param Roles $rolesModel
     * @return void
     */
    public function __construct(RolePermissions $model, Permissions $permissionsModel, Roles $rolesModel)
    {
        $this->model            = $model;
        $this->permissionsModel = $permissionsModel;
        $this->rolesModel       = $rolesModel;
    }

    /**
     * get Model Table Name in This Repository.
     *
     * @return string
     */
    public function getTable(): string
    {
        return $this->model->getTable();
    }

    /**
     * get All Role Permissions Data.
     *
     * @param int $roleId id of role
     * @return Collection
     */
    public function getByRoleId(int $roleId): Collection
    {
        $rolePermissions = $this->getTable();
        $roles = $this->rolesModel->getTable();

        return DB::table($rolePermissions)
            ->select([$rolePermissions . '.id', $rolePermissions . '.role_id', $rolePermissions . '.permission_id', $roles .'.name', $roles . '.code'])
            ->leftJoin($roles, $rolePermissions . '.role_id', '=', $roles . '.id')
            ->where('role_id', '=', [$roleId])
            ->where($rolePermissions . '.deleted_at', '=', null)
            ->get();
    }
    /**
     * create Role Permissions data.
     *
     * @param array $resource create data
     * @return int
     */
    public function createRolePermission(array $resource): int
    {
        return DB::table($this->getTable())->insert($resource);
    }

    /**
     * update Role Permissions data.
     *
     * @param array $resource update data
     * @param int $roleId id of role
     * @return int
     */
    public function updateRolePermissionsData(array $resource, int $roleId): int
    {
        // role_permissions
        $rolePermissions = $this->model->getTable();

        // Query Builder???update
        return DB::table($rolePermissions)
            // ->whereIn('id', [$id])
            ->where(RolePermissions::ROLE_ID, '=', [$roleId])
            ->where(RolePermissions::DELETED_AT, '=', null)
            ->update($resource);
    }

    /**
     * delete Role Permissions data.
     *
     * @param array $resource update data
     * @param int $roleId $id id of role
     * @return int
     */
    public function deleteRolePermissionsData(array $resource, int $roleId): int
    {
        // Query Builder???update
        return DB::table($this->getTable())
            // ->whereIn('id', [$id])
            ->where(RolePermissions::ROLE_ID, '=', $roleId)
            ->where(RolePermissions::DELETED_AT, '=', null)
            ->update($resource);
    }

    /**
     * delete Role Permissions data by array data.
     *
     * @param array $resource update data
     * @param array $ids $ids id of records
     * @return int
     */
    public function deleteRolePermissionsByIds(array $resource, array $ids): int
    {
        return DB::table($this->getTable())
            ->whereIn(RolePermissions::ROLE_ID, $ids)
            ->where(RolePermissions::DELETED_AT, '=', null)
            ->update($resource);
    }
}
