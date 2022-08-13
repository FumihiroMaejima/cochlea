<?php

namespace App\Repositories\Admins\Roles;

use Illuminate\Support\Collection;

interface RolesRepositoryInterface
{
    public function getTable(): string;

    public function getRoles(): Collection;

    public function getRolesList(): Collection;

    public function getLatestRole(): object;

    public function create(array $resource): int;

    public function update(int $id, array $resource): int;

    public function deleteByIds(array $ids, array $resource): int;
}
