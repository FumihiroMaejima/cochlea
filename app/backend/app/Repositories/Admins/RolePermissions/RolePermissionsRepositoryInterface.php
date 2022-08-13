<?php

namespace App\Repositories\Admins\RolePermissions;

use Illuminate\Support\Collection;

interface RolePermissionsRepositoryInterface
{
    public function getTable(): string;

    public function getByRoleId(int $id): Collection;

    public function create(array $resource): int;

    public function update(array $resource, int $id): int;

    public function delete(array $resource, int $id): int;

    public function deleteRolePermissionsByIds(array $resource, array $ids): int;
}
