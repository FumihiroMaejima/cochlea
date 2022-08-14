<?php

namespace App\Repositories\Admins\Permissions;

use App\Exceptions\MyApplicationHttpException;
use App\Exceptions\ExceptionStatusCodeMessages;
use App\Models\Masters\Permissions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class PermissionsRepository implements PermissionsRepositoryInterface
{
    protected Permissions $model;

    private const NO_DATA_COUNT = 0;
    private const FIRST_DATA_COUNT = 1;

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
     * get by id.
     *
     * @param int $id rocord id
     * @param bool $isLock exec lock For Update
     * @return Collection|null
     * @throws MyApplicationHttpException
     */
    public function getById(int $id, bool $isLock = false): Collection|null
    {
        $collection = DB::table($this->getTable())
            ->select(['*'])
            ->where(Permissions::ID, '=', $id)
            ->where(Permissions::DELETED_AT, '=', null)
            ->get();

        // 存在しない場合
        if ($collection->count() === self::NO_DATA_COUNT) {
            return null;
        }

        // 複数ある場合
        if ($collection->count() > self::FIRST_DATA_COUNT) {
            throw new MyApplicationHttpException(
                ExceptionStatusCodeMessages::STATUS_CODE_500,
                'has deplicate collections,'
            );
        }

        if ($isLock) {
            // ロックをかけた状態で再検索
            $collection = DB::table($this->getTable())
            ->lockForUpdate()
            ->select(['*'])
            ->where(Permissions::ID, '=', $id)
            ->where(Permissions::DELETED_AT, '=', null)
            ->get();
        }

        return $collection;
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
