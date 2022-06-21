<?php

namespace App\Repositories\Admins;

use App\Models\Masters\Admins;
use App\Models\AdminsRoles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class AdminsRepository implements AdminsRepositoryInterface
{
    protected Admins $model;
    protected AdminsRoles $adminsRolesModel;

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
     * create Admin data.
     *
     * @return int
     */
    public function createAdmin(array $resource): int
    {
        return DB::table($this->getTable())->insert($resource);
    }

    /**
     * update Admin data.
     *
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
     * @param array $resource
     * @param int $id
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
}
