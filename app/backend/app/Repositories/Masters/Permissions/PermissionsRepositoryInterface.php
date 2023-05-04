<?php

namespace App\Repositories\Masters\Permissions;

use Illuminate\Support\Collection;

interface PermissionsRepositoryInterface
{
    public function getTable(): string;

    public function getPermissions(): Collection;

    public function getPermissionsList(): Collection;

    public function getById(int $id, bool $isLock = false): Collection|null;

    public function create(array $resource): int;

    public function update(int $id, array $resource): int;

    public function delete(int $id, array $resource): int;
}