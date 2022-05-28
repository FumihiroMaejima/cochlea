<?php

namespace App\Repositories\Admins\Roles;

use App\Models\Roles;
use App\Models\RolePermissions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class RolesRepository implements RolesRepositoryInterface
{
    protected $model;
    protected $rolePermissionsModel;

    /**
     * Create a new RolesRepository instance.
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
     *
     * @return Collection
     */
    public function getRoles(): Collection
    {
        // roles
        $roles = $this->model->getTable();
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
     * Get Roles as List.
     *
     * @return Collection
     */
    public function getRolesList(): Collection
    {
        // roles
        $roles = $this->model->getTable();

        // collection
        return DB::table($roles)
            ->select([$roles . '.id', $roles . '.name'])
            ->get();
    }

    /**
     * get Latest Role data.
     *
     * @return \Illuminate\Database\Eloquent\Model|object|static|null
     */
    public function getLatestRole(): object
    {
        return DB::table($this->model->getTable())
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
        return DB::table($this->model->getTable())->insert($resource);
    }

    /**
     * update Role data.
     *
     * @return int
     */
    public function updateRoleData(array $resource, int $id): int
    {
        // roles
        $roles = $this->model->getTable();

        // Query Builderのupdate
        return DB::table($roles)
            // ->whereIn('id', [$id])
            ->where('id', '=', [$id])
            ->where('deleted_at', '=', null)
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
        // roles
        $roles = $this->model->getTable();

        // Query Builderのupdate
        return DB::table($roles)
            ->whereIn('id', $ids)
            // ->where('id', '=', $id)
            ->where('deleted_at', '=', null)
            ->update($resource);
    }
}
