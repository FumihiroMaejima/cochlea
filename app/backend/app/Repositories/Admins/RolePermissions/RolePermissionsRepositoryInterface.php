<?php

namespace App\Repositories\Admins\RolePermissions;

use Illuminate\Support\Collection;

interface RolePermissionsRepositoryInterface
{
    public function getTable(): string;

    public function getByRoleId(int $id): Collection;

    public function createRolePermission(array $resource): int;

    public function updateRolePermissionsData(array $resource, int $id): int;

    public function deleteRolePermissionsData(array $resource, int $id): int;

    public function deleteRolePermissionsByIds(array $resource, array $ids): int;
}
