<?php

namespace App\Repositories\Users\UserCoins;

use Illuminate\Support\Collection;

interface UserCoinsRepositoryInterface
{
    public function getTable(int $userId): string;

    public function getByUserId(int $userId): Collection|null;

    public function createUserCoins(int $userId, array $resource): int;

    public function updateUserCoins(int $userId, array $resource): int;

    public function deleteUserCoins(int $userId, array $resource): int;
}
