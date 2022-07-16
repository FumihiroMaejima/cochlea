<?php

namespace App\Repositories\Users\UserCoins;

use Illuminate\Support\Collection;

interface UserCoinsRepositoryInterface
{
    public function getTable(): string;

    public function getByUserId(int $userId): Collection|null;

    public function createUserCoins(array $resource): int;

    public function updateUserCoins(array $resource, int $userId): int;

    public function deleteUserCoins(array $resource, int $userId): int;
}
