<?php

namespace App\Repositories\Admins;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

interface AdminsRepositoryInterface
{
    public function getTable(): string;

    public function getAdmins(): Collection;

    public function getAdminsList(): Collection;

    public function getLatestAdmin(): object;

    public function createAdmin(array $resource): int;

    public function updateAdminData(array $resource, int $id): int;

    public function deleteAdminData(array $resource, int $id): int;
}
