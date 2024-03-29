<?php

declare(strict_types=1);

namespace App\Repositories\Masters\AdminsRoles;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

interface AdminsRolesRepositoryInterface
{
    public function getTable(): string;

    public function getByAdminId(int $id): Collection;

    public function create(array $resource): bool;

    public function update(int $id, array $resource): int;

    public function delete(int $id, array $resource): int;
}
