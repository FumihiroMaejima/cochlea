<?php

namespace App\Repositories\Users\Users;

use Illuminate\Support\Collection;
use Illuminate\Database\Query\Builder;

interface UsersRepositoryInterface
{
    public function getTable(): string;

    public function getByUserId(int $userId, bool $isLock = false): Collection|null;

    public function getByEmail(string $email, bool $isLock = false): array|null;

    public function getListByUserId(int $userId): Collection;

    public function create(array $resource): int;

    public function update(int $userId, string $createdAt, array $resource): int;

    public function delete(int $userId, string $createdAt, array $resource): int;
}
