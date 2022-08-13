<?php

namespace App\Repositories\Admins\Roles;

use Illuminate\Support\Collection;

interface RolesRepositoryInterface
{
    public function getTable(): string;

    public function getRoles(): Collection;

    public function getRolesList(): Collection;

    public function getLatestRole(): object;

    public function createRole(array $resource): int;

    public function updateRoleData(int $id, array $resource): int;

    public function deleteRoleData(array $ids, array $resource): int;
}
