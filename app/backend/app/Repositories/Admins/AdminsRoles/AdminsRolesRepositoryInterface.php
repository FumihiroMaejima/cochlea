<?php

namespace App\Repositories\Admins\AdminsRoles;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

interface AdminsRolesRepositoryInterface
{
    public function getTable(): string;

    public function getByAdminId(int $id): Collection;

    public function createAdminsRole(array $resource): int;

    public function updateAdminsRoleData(array $resource, int $id): int;

    public function deleteAdminsRoleData(array $resource): int;
}
