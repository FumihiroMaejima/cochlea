<?php

namespace App\Repositories\Masters\RolePermissions;

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
     * create instance.
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
     * get By role id.
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
     * create recode.
     *
     * @param array $resource create data
     * @return int
     */
    public function create(array $resource): int
    {
        return DB::table($this->getTable())->insert($resource);
    }

    /**
     * update recode.
     *
     * @param int $roleId id of role
     * @param array $resource update data
     * @return int
     */
    public function update(int $roleId, array $resource): int
    {
        // role_permissions
        $rolePermissions = $this->model->getTable();

        // Query Builderのupdate
        return DB::table($rolePermissions)
            // ->whereIn('id', [$id])
            ->where(RolePermissions::ROLE_ID, '=', $roleId)
            ->where(RolePermissions::DELETED_AT, '=', null)
            ->update($resource);
    }

    /**
     * delete recode.
     *
     * @param int $roleId id of role
     * @param array $resource update data
     * @return int
     */
    public function delete(int $roleId, array $resource): int
    {
        // Query Builderのupdate
        return DB::table($this->getTable())
            // ->whereIn('id', [$id])
            ->where(RolePermissions::ROLE_ID, '=', $roleId)
            ->where(RolePermissions::DELETED_AT, '=', null)
            ->update($resource);
    }

    /**
     * delete recode by recode ids.
     *
     * @param array $ids $ids id of records
     * @param array $resource update data
     * @return int
     */
    public function deleteByIds(array $ids, array $resource): int
    {
        return DB::table($this->getTable())
            ->whereIn(RolePermissions::ROLE_ID, $ids)
            ->where(RolePermissions::DELETED_AT, '=', null)
            ->update($resource);
    }
}
