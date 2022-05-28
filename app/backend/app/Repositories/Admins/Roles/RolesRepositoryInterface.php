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

    public function updateRoleData(array $resource, int $id): int;

    public function deleteRoleData(array $resource, array $ids): int;
}
