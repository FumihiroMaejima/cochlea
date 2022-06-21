<?php

namespace App\Repositories\Admins\RolePermissions;

use App\Models\Roles;
use App\Models\Masters\Permissions;
use App\Models\RolePermissions;
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
     * get All Role Data.
     * @param int $roleId
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
     * create Admin data.
     * @param array $resource
     * @return int
     */
    public function createRolePermission(array $resource): int
    {
        return DB::table($this->getTable())->insert($resource);
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
            ->where(RolePermissions::ROLE_ID, '=', [$roleId])
            ->where(RolePermissions::DELETED_AT, '=', null)
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
        // Query Builderのupdate
        return DB::table($this->getTable())
            // ->whereIn('id', [$id])
            ->where(RolePermissions::ROLE_ID, '=', $roleId)
            ->where(RolePermissions::DELETED_AT, '=', null)
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
        return DB::table($this->getTable())
            ->whereIn(RolePermissions::ROLE_ID, $ids)
            ->where(RolePermissions::DELETED_AT, '=', null)
            ->update($resource);
    }
}
