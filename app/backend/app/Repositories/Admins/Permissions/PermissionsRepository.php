<?php

namespace App\Repositories\Admins\Permissions;

use App\Models\Masters\Permissions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class PermissionsRepository implements PermissionsRepositoryInterface
{
    protected Permissions $model;

    /**
     * create instance.
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
     * get All recodes.
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
     * @param array $id id of record.
     * @param array $resource update data
     * @return int
     */
    public function update(int $id, array $resource): int
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
     * delete recode.
     *
     * @param int $id id of record.
     * @param array $resource update data
     * @return int
     */
    public function delete(int $id, array $resource): int
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
