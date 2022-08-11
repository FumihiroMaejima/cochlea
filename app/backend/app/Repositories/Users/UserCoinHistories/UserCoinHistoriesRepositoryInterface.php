<?php

namespace App\Repositories\Users\UserCoinHistories;

use Illuminate\Support\Collection;
use Illuminate\Database\Query\Builder;

interface UserCoinHistoriesRepositoryInterface
{
    public function getTable(int $userId): string;

    public function getQueryBuilder(int $userId): Builder;

    public function getByUserId(int $userId): Collection|null;

    public function create(int $userId, array $resource): int;

    public function update(int $userId, string $createdAt, array $resource): int;

    public function delete(int $userId, string $createdAt, array $resource): int;
}
