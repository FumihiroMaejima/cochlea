<?php

namespace App\Repositories\Admins\Roles;

use App\Models\Roles;
use App\Models\RolePermissions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class RolesRepository implements RolesRepositoryInterface
{
    protected Roles $model;
    protected RolePermissions $rolePermissionsModel;

    /**
     * create a new RolesRepository instance.
     * @param \App\Models\Roles $model
     * @param \App\Models\RolePermissions $rolePermissions
     * @return void
     */
    public function __construct(Roles $model, RolePermissions $rolePermissionsModel)
    {
        $this->model = $model;
        $this->rolePermissionsModel = $rolePermissionsModel;
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
     *
     * @return Collection
     */
    public function getRoles(): Collection
    {
        // roles
        $roles = $this->getTable();
        // role_permissions
        $rolePermissions = $this->rolePermissionsModel->getTable();

        // collection
        return DB::table($roles)
            ->select([$roles . '.id', $roles . '.name', $roles . '.code', $roles . '.detail', DB::raw('group_concat(' . $rolePermissions . '.permission_id) as permissions')])
            ->leftJoin($rolePermissions, $roles . '.id', '=', $rolePermissions . '.role_id')
            ->where($roles . '.deleted_at', '=', null)
            ->where($rolePermissions . '.deleted_at', '=', null)
            ->groupBy($roles . '.id')
            ->get();
    }

    /**
     * get Roles as List.
     *
     * @return Collection
     */
    public function getRolesList(): Collection
    {
        // roles
        $roles = $this->getTable();

        // collection
        return DB::table($roles)
            ->select([$roles . '.id', $roles . '.name'])
            ->where($roles . '.deleted_at', '=', null)
            ->get();
    }

    /**
     * get Latest Role data.
     *
     * @return \Illuminate\Database\Eloquent\Model|object|static|null
     */
    public function getLatestRole(): object
    {
        return DB::table($this->getTable())
            ->latest()
            ->first();
    }

    /**
     * create Admin data.
     *
     * @return int
     */
    public function createRole(array $resource): int
    {
        return DB::table($this->getTable())->insert($resource);
    }

    /**
     * update Role data.
     *
     * @return int
     */
    public function updateRoleData(array $resource, int $id): int
    {
        // Query Builderのupdate
        return DB::table($this->getTable())
            // ->whereIn('id', [$id])
            ->where(Roles::ID, '=', [$id])
            ->where(Roles::DELETED_AT, '=', null)
            ->update($resource);
    }

    /**
     * delete Role data.
     * @param array $resource
     * @param array $ids
     * @return int
     */
    public function deleteRoleData(array $resource, array $ids): int
    {
        // Query Builderのupdate
        return DB::table($this->getTable())
            ->whereIn(Roles::ID, $ids)
            // ->where('id', '=', $id)
            ->where(Roles::DELETED_AT, '=', null)
            ->update($resource);
    }
}
