<?php

declare(strict_types=1);

namespace App\Repositories\Masters;

use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Models\Masters\Admins;
use App\Models\Masters\AdminsRoles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class AdminsRepository implements AdminsRepositoryInterface
{
    protected Admins $model;
    protected AdminsRoles $adminsRolesModel;

    private const NO_DATA_COUNT = 0;
    private const FIRST_DATA_COUNT = 1;

    /**
     * create instance.
     *
     * @param \App\Models\Admins $model
     * @param \App\Models\AdminsRoles $adminsRolesModel
     * @return void
     */
    public function __construct(Admins $model, AdminsRoles $adminsRolesModel)
    {
        $this->model = $model;
        $this->adminsRolesModel = $adminsRolesModel;
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
     * Get All recodes.
     *
     * @return Collection
     */
    public function getAdmins(): Collection
    {
        return DB::table($this->getTable())->get();
        // Eloquent
        // return Admins::get();
    }

    /**
     * Get recodes as List.
     *
     * @return Collection
     */
    public function getAdminsList(): Collection
    {
        // admins
        $admins = $this->getTable();
        // admins_roles
        $adminsRoles = $this->adminsRolesModel->getTable();

        // collection
        return DB::table($admins)
            ->select([$admins . '.id', $admins . '.name', $admins . '.email', $adminsRoles . '.role_id as roleId'])
            ->leftJoin($adminsRoles, $admins.'.id', '=', $adminsRoles.'.admin_id')
            ->where($admins . '.deleted_at', '=', null)
            ->get();

        /*
        // sql
        $sql = DB::table($admins)
            ->select($selectColumn)
            ->leftJoin($adminsRoles, $admins . '.id', '=', $adminsRoles . '.admin_id')
            ->toSql();

        // Builder class
        $builder = DB::table($admins)
            ->select($selectColumn)
            ->leftJoin($adminsRoles, $admins . '.id', '=', $adminsRoles . '.admin_id');

        // get query log
        DB::enableQueryLog();
        $sql = DB::table($admins)
            ->select($selectColumn)
            ->leftJoin($adminsRoles, $admins . '.id', '=', $adminsRoles . '.admin_id')
            ->get();
        $log = DB::getQueryLog();
        */
    }

    /**
     * get Latest Admin recode.
     *
     * @return \Illuminate\Database\Eloquent\Model|object|static|null
     */
    public function getLatestAdmin(): object
    {
        return DB::table($this->getTable())
            ->where(Admins::DELETED_AT, '=', null)
            ->orderBy(Admins::ID, 'DESC')
            ->limit(1)
            ->get();
        // ->get();
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
            ->where(Admins::ID, '=', $id)
            ->where(Admins::DELETED_AT, '=', null)
            ->get();

        // 存在しない場合
        if ($collection->count() === self::NO_DATA_COUNT) {
            return null;
        }

        // 複数ある場合
        if ($collection->count() > self::FIRST_DATA_COUNT) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_500,
                'has deplicate collections,'
            );
        }

        if ($isLock) {
            // ロックをかけた状態で再検索
            $collection = DB::table($this->getTable())
            ->select(['*'])
            ->where(Admins::ID, '=', $id)
            ->where(Admins::DELETED_AT, '=', null)
            ->lockForUpdate()
            ->get();
        }

        return $collection;
    }

    /**
     * get by mail address.
     *
     * @param int $email mail address
     * @param bool $isLock exec lock For Update
     * @return Collection|null
     * @throws MyApplicationHttpException
     */
    public function getByEmail(string $email, bool $isLock = false): ?Collection
    {
        $query = DB::table($this->getTable())
            ->select(['*'])
            ->where(Admins::EMAIL, '=', $email)
            ->where(Admins::DELETED_AT, '=', null);

        if ($isLock) {
            $query->lockForUpdate();
        }

        $collection = $query->get();

        // 存在しない場合
        if ($collection->count() === self::NO_DATA_COUNT) {
            return null;
        }

        // 複数ある場合
        if ($collection->count() > self::FIRST_DATA_COUNT) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_500,
                'has deplicate collections,'
            );
        }

        return $collection;
    }

    /**
     * create recode.
     *
     * @param array $resource create data
     * @return bool create row count
     */
    public function create(array $resource): bool
    {
        return DB::table($this->getTable())->insert($resource);
    }

    /**
     * update recode.
     *
     * @param array $id of record
     * @param array $resource update data
     * @return int update row count
     */
    public function update(int $id, array $resource): int
    {
        // admins
        $admins = $this->getTable();

        // Query Builderのupdate
        return DB::table($admins)
            // ->whereIn('id', [$id])
            ->where(Admins::ID, '=', [$id])
            ->where(Admins::DELETED_AT, '=', null)
            ->update($resource);

        /* $keys = ['name', 'email'];
        $template = [
            $keys[0] => '\'' . $request->input($keys[0]) . '\'',
            $keys[1] => '\'' . $request->input($keys[1]) . '\''
        ];

        $bindings = ' SET ';
        foreach ($template as $key => $item) {
            $suffix = $key !== $keys[1] ? ', ' : '';
            $bindings = $bindings . $key . ' = ' . $item . $suffix;
        }

        Log::info(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'bindings: ' . json_encode($bindings));
        $query = 'UPDATE ' . $admins . $bindings . ' where id = ?';

        // Facadeのupdate
        return DB::update($query, [$id]); */
    }

    /**
     * delete recode (set deleted date & flag).
     *
     * @param int $id id of record
     * @param array $resource update data
     * @return int update row count
     */
    public function delete(int $id, array $resource): int
    {
        // admins
        $admins = $this->getTable();

        // Query Builderのupdate
        return DB::table($admins)
            // ->whereIn('id', [$id])
            ->where(Admins::ID, '=', $id)
            ->where(Admins::DELETED_AT, '=', null)
            ->update($resource);
    }

    /**
     * update Admin password.
     *
     * @param array $id of admin
     * @param array $resource update data
     * @return int update row count
     */
    public function updatePassword(int $id, array $resource): int
    {
        return DB::table($this->getTable())
            ->where(Admins::ID, '=', $id)
            ->where(Admins::DELETED_AT, '=', null)
            ->update($resource);
    }
}
