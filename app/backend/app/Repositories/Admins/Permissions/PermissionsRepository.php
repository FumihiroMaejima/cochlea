<?php

namespace App\Repositories\Admins\Permissions;

use App\Models\Roles;
use App\Models\Permissions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class PermissionsRepository implements PermissionsRepositoryInterface
{
    protected $model;

    /**
     * Create a new PermissionsRepository instance.
     * @param \App\Models\Permissions $model
     * @return void
     */
    public function __construct(Permissions $model)
    {
        $this->model = $model;
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
    public function getPermissions(): Collection
    {
        return DB::table($this->model->getTable())->get();
    }

    /**
     * Get Roles as List.
     *
     * @return Collection
     */
    public function getPermissionsList(): Collection
    {
        // permissions
        $permissions = $this->model->getTable();

        // collection
        return DB::table($permissions)
            ->select([$permissions . '.id', $permissions . '.name'])
            ->get();
    }

    /**
     * create Admin data.
     *
     * @return int
     */
    public function createPermission(array $resource): int
    {
        return DB::table($this->model->getTable())->insert($resource);
    }

    /**
     * update Role data.
     *
     * @return int
     */
    public function updatePermissionData(array $resource, int $id): int
    {
        // permissions
        $permissions = $this->model->getTable();

        // Query Builderのupdate
        return DB::table($permissions)
            // ->whereIn('id', [$id])
            ->where('id', '=', [$id])
            ->where('deleted_at', '=', null)
            ->update($resource);
    }

    /**
     * delete Role data.
     * @param array $resource
     * @param int $id
     * @return int
     */
    public function deletePermissionsData(array $resource, int $id): int
    {
        // permissions
        $permissions = $this->model->getTable();

        // Query Builderのupdate
        return DB::table($permissions)
            // ->whereIn('id', [$id])
            ->where('id', '=', $id)
            ->where('deleted_at', '=', null)
            ->update($resource);
    }
}
