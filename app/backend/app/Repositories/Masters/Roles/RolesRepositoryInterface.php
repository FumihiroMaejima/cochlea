<?php

declare(strict_types=1);

namespace App\Repositories\Masters\Roles;

use Illuminate\Support\Collection;

interface RolesRepositoryInterface
{
    public function getTable(): string;

    public function getRoles(): Collection;

    public function getRolesList(): Collection;

    public function getLatestRole(): object;

    public function getById(int $id, bool $isLock = false): Collection|null;

    public function getByIds(array $ids, bool $isLock = false): Collection|null;

    public function create(array $resource): bool;

    public function update(int $id, array $resource): int;

    public function deleteByIds(array $ids, array $resource): int;
}
