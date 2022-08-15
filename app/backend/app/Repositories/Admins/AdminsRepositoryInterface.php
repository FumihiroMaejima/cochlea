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

    public function getById(int $id, bool $isLock = false): Collection|null;

    public function getByEmail(string $email): Collection|null;

    public function create(array $resource): int;

    public function update(int $id, array $resource): int;

    public function delete(int $id, array $resource): int;

    public function updatePassword(int $id, array $resource): int;
}
