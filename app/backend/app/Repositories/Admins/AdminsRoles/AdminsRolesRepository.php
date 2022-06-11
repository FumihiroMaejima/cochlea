<?php

namespace App\Repositories\Admins\AdminsRoles;

use App\Models\Admins;
use App\Models\AdminsRoles;
use App\Models\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class AdminsRolesRepository implements AdminsRolesRepositoryInterface
{
    protected $model;
    protected $adminsModel;
    protected $rolesModel;

    /**
     * create a new AuthInfoController instance.
     *
     * @return void
     */
    public function __construct(AdminsRoles $model, Admins $adminsModel, Roles $rolesModel)
    {
        $this->model = $model;
        $this->adminsModel = $adminsModel;
        $this->rolesModel = $rolesModel;
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
     * update Admins Role.
     *
     * @return Collection
     */
    public function getByAdminId(int $adminId): Collection
    {
        // admins
        $adminsRoles = $this->getTable();
        $roles = $this->rolesModel->getTable();

        // Query Builderのupdate
        return DB::table($adminsRoles)
            ->select([$adminsRoles . '.id', $adminsRoles . '.role_id', $adminsRoles . '.admin_id', $roles . '.code'])
            ->leftJoin($roles, $adminsRoles . '.role_id', '=', $roles . '.id')
            ->where('admin_id', '=', [$adminId])
            ->where($adminsRoles . '.deleted_at', '=', null)
            ->get();
    }

    /**
     * update Admins Role.
     *
     * @return int
     */
    public function createAdminsRole(array $resource): int
    {
        return DB::table($this->getTable())->insert($resource);
    }

    /**
     * update Admins Role.
     *
     * @param array $resource
     * @param int $adminId
     * @return int
     */
    public function updateAdminsRoleData(array $resource, int $adminId): int
    {

       /*  $table->foreignId('admin_id')->constrained('admins')->comment('管理者ID');
        $table->foreignId('role_id')->constrained('role')->comment('ロールID'); */

        // Query Builderのupdate
        return DB::table($this->getTable())
            ->where(AdminsRoles::ADMIN_ID, '=', [$adminId])
            ->where(AdminsRoles::DELETED_AT, '=', null)
            ->update($resource);
    }

    /**
     * delete Admins Role.
     *
     * @param array $resource
     * @return int
     */
    public function deleteAdminsRoleData(array $resource): int
    {
        // Query Builderのupdate
        return DB::table($this->getTable())
            ->where(AdminsRoles::ADMIN_ID, '=', [$resource['admin_id']])
            ->where(AdminsRoles::DELETED_AT, '=', null)
            ->update($resource);
    }
}
