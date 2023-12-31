<?php

declare(strict_types=1);

namespace App\Repositories\Masters\AdminsRoles;

use App\Models\Masters\Admins;
use App\Models\Masters\AdminsRoles;
use App\Models\Masters\Roles;
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
     * create instance.
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
     * get by Admin id.
     *
     * @param int $adminId admin id
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
     * create recode.
     *
     * @param array $resource create data
     * @return bool
     */
    public function create(array $resource): bool
    {
        return DB::table($this->getTable())->insert($resource);
    }

    /**
     * update recode.
     *
     * @param int $adminId id of admin
     * @param array $resource update data
     * @return int
     */
    public function update(int $adminId, array $resource): int
    {
        /*  $table->foreignId('admin_id')->constrained('admins')->comment('管理者ID');
         $table->foreignId('role_id')->constrained('role')->comment('ロールID'); */

        // Query Builderのupdate
        return DB::table($this->getTable())
            ->where(AdminsRoles::ADMIN_ID, '=', $adminId)
            ->where(AdminsRoles::DELETED_AT, '=', null)
            ->update($resource);
    }

    /**
     * delete recode.
     *
     * @param int $adminId id of admin
     * @param array $resource update data
     * @return int
     */
    public function delete(int $adminId, array $resource): int
    {
        // Query Builderのupdate
        return DB::table($this->getTable())
            ->where(AdminsRoles::ADMIN_ID, '=', $adminId)
            ->where(AdminsRoles::DELETED_AT, '=', null)
            ->update($resource);
    }
}
