<?php

declare(strict_types=1);

namespace App\Repositories\Masters;

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

    public function create(array $resource): bool;

    public function update(int $id, array $resource): int;

    public function delete(int $id, array $resource): int;

    public function updatePassword(int $id, array $resource): int;
}
