<?php

namespace App\Repositories\Admins\Permissions;

use Illuminate\Support\Collection;

interface PermissionsRepositoryInterface
{
    public function getTable(): string;

    public function getPermissions(): Collection;

    public function getPermissionsList(): Collection;

    public function createPermission(array $resource): int;

    public function updatePermissionData(int $id, array $resource): int;

    public function deletePermissionsData(int $id, array $resource): int;
}
