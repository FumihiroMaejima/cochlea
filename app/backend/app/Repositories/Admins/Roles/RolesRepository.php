<?php

namespace App\Repositories\Admins\Roles;

use App\Exceptions\MyApplicationHttpException;
use App\Exceptions\ExceptionStatusCodeMessages;
use App\Models\Masters\Roles;
use App\Models\Masters\RolePermissions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class RolesRepository implements RolesRepositoryInterface
{
    protected Roles $model;
    protected RolePermissions $rolePermissionsModel;

    private const NO_DATA_COUNT = 0;
    private const FIRST_DATA_COUNT = 1;

    /**
     * create instance.
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
     * get All recodes.
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
     * get recodes as List.
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
     * get Latest record.
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
            ->where(Roles::ID, '=', $id)
            ->where(Roles::DELETED_AT, '=', null)
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
            ->where(Roles::ID, '=', $id)
            ->where(Roles::DELETED_AT, '=', null)
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
     * @param array $id id of record
     * @param array $resource update data
     * @return int
     */
    public function update(int $id, array $resource): int
    {
        // Query Builderのupdate
        return DB::table($this->getTable())
            // ->whereIn('id', [$id])
            ->where(Roles::ID, '=', [$id])
            ->where(Roles::DELETED_AT, '=', null)
            ->update($resource);
    }

    /**
     * delete recode by ids.
     *
     * @param array $ids id of records
     * @param array $resource update data
     * @return int
     */
    public function deleteByIds(array $ids, array $resource): int
    {
        // Query Builderのupdate
        return DB::table($this->getTable())
            ->whereIn(Roles::ID, $ids)
            // ->where('id', '=', $id)
            ->where(Roles::DELETED_AT, '=', null)
            ->update($resource);
    }
}
