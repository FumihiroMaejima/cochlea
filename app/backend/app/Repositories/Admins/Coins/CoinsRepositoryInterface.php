<?php

namespace App\Repositories\Admins\Coins;

use Illuminate\Support\Collection;

interface CoinsRepositoryInterface
{
    public function getTable(): string;

    public function getCoins(): Collection;

    public function getCoinsList(): Collection;

    public function getLatestCoin(): object;

    public function getById(int $id): Collection|null;

    public function createCoin(array $resource): int;

    public function updateCoin(int $id, array $resource): int;

    public function deleteCoin(array $ids, array $resource): int;
}
