<?php

namespace App\Repositories\Masters\RolePermissions;

use Illuminate\Support\Collection;

interface RolePermissionsRepositoryInterface
{
    public function getTable(): string;

    public function getByRoleId(int $id): Collection;

    public function create(array $resource): int;

    public function update(int $id, array $resource): int;

    public function delete(int $id, array $resource): int;

    public function deleteByIds(array $ids, array $resource): int;
}
