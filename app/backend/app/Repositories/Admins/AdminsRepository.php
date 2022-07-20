<?php

namespace App\Repositories\Admins;

use App\Exceptions\MyApplicationHttpException;
use App\Exceptions\ExceptionStatusCodeMessages;
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
     * create a new AdminsRepository instance.
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
     * Get All Admins Data.
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
     * Get Admins as List.
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
     * get Latest Admin data.
     *
     * @return \Illuminate\Database\Eloquent\Model|object|static|null
     */
    public function getLatestAdmin(): object
    {
        return DB::table($this->getTable())
            ->latest()
            // ->where('deleted_at', '=', null)
            ->first();
        // ->get();
    }

    /**
     * get by admin id.
     *
     * @param int $adminId admin id
     * @return Collection|null
     * @throws MyApplicationHttpException
     */
    public function getById(int $id): Collection|null
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
                ExceptionStatusCodeMessages::STATUS_CODE_500,
                'has deplicate collections,'
            );
        }

        return $collection;
    }

    /**
     * create Admin data.
     *
     * @param array $resource create data
     * @return int
     */
    public function createAdmin(array $resource): int
    {
        return DB::table($this->getTable())->insert($resource);
    }

    /**
     * update Admin data.
     *
     * @param array $resource update data
     * @param array $id of record
     * @return int
     */
    public function updateAdminData(array $resource, int $id): int
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
     * delete Admin data (set deleted date & flag).
     *
     * @param array $resource update data
     * @param int $id id of record
     * @return int
     */
    public function deleteAdminData(array $resource, int $id): int
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
     * @return int
     */
    public function updatePassword(int $id, array $resource): int
    {
        return DB::table($$this->getTable())
            ->where(Admins::ID, '=', $id)
            ->where(Admins::DELETED_AT, '=', null)
            ->update($resource);
    }
}
