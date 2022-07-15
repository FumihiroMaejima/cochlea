<?php

namespace App\Repositories\Admins\Permissions;

use App\Models\Masters\Roles;
use App\Models\Masters\Permissions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class PermissionsRepository implements PermissionsRepositoryInterface
{
    protected Permissions $model;

    /**
     * create a new PermissionsRepository instance.
     * @param \App\Models\Permissions $model
     * @return void
     */
    public function __construct(Permissions $model)
    {
        $this->model = $model;
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
     * get All Permissions Data.
     *
     * @return Collection
     */
    public function getPermissions(): Collection
    {
        return DB::table($this->model->getTable())->get();
    }

    /**
     * get Permissions as List.
     *
     * @return Collection
     */
    public function getPermissionsList(): Collection
    {
        // permissions
        $permissions = $this->getTable();

        // collection
        return DB::table($permissions)
            ->select([$permissions . '.id', $permissions . '.name'])
            ->get();
    }

    /**
     * create Permission data.
     *
     * @param array $resource create data
     * @return int
     */
    public function createPermission(array $resource): int
    {
        return DB::table($this->getTable())->insert($resource);
    }

    /**
     * update Permission data.
     *
     * @param array $resource update data
     * @param array $id id of record.
     * @return int
     */
    public function updatePermissionData(array $resource, int $id): int
    {
        // permissions
        $permissions = $this->getTable();

        // Query Builderのupdate
        return DB::table($permissions)
            // ->whereIn('id', [$id])
            ->where(Permissions::ID, '=', [$id])
            ->where(Permissions::DELETED_AT, '=', null)
            ->update($resource);
    }

    /**
     * delete Permission data.
     *
     * @param array $resource update data
     * @param int $id id of record.
     * @return int
     */
    public function deletePermissionsData(array $resource, int $id): int
    {
        // permissions
        $permissions = $this->getTable();

        // Query Builderのupdate
        return DB::table($permissions)
            // ->whereIn('id', [$id])
            ->where(Permissions::ID, '=', $id)
            ->where(Permissions::DELETED_AT, '=', null)
            ->update($resource);
    }
}
