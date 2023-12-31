<?php

declare(strict_types=1);

namespace App\Repositories\Masters\RolePermissions;

use Illuminate\Support\Collection;

interface RolePermissionsRepositoryInterface
{
    public function getTable(): string;

    public function getByRoleId(int $id): Collection;

    public function create(array $resource): bool;

    public function update(int $id, array $resource): int;

    public function delete(int $id, array $resource): int;

    public function deleteByIds(array $ids, array $resource): int;
}
