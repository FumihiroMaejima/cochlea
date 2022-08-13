<?php

namespace App\Repositories\Admins\AdminsRoles;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

interface AdminsRolesRepositoryInterface
{
    public function getTable(): string;

    public function getByAdminId(int $id): Collection;

    public function create(array $resource): int;

    public function update(int $id, array $resource): int;

    public function delete(array $resource): int;
}
