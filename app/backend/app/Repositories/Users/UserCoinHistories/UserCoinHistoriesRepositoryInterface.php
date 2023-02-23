<?php

namespace App\Repositories\Users\UserCoinHistories;

use Illuminate\Support\Collection;
use Illuminate\Database\Query\Builder;

interface UserCoinHistoriesRepositoryInterface
{
    public function getByUserId(int $userId, bool $isLock = false): Collection|null;

    public function getListByUserId(int $userId): Collection;

    public function getByUserIdAndUuId(int $userId, string $uuid, bool $isLock = false): Collection|null;

    public function create(int $userId, array $resource): int;

    public function update(int $userId, string $createdAt, array $resource): int;

    public function delete(int $userId, string $createdAt, array $resource): int;
}
