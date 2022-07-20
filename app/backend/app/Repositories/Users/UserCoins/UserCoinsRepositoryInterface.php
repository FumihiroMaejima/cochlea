<?php

namespace App\Repositories\Users\UserCoins;

use Illuminate\Support\Collection;
use Illuminate\Database\Query\Builder;

interface UserCoinsRepositoryInterface
{
    public function getTable(int $userId): string;

    public function getQueryBuilder(int $userId): Builder;

    public function getByUserId(int $userId): Collection|null;

    public function createUserCoins(int $userId, array $resource): int;

    public function updateUserCoins(int $userId, array $resource): int;

    public function deleteUserCoins(int $userId, array $resource): int;
}
