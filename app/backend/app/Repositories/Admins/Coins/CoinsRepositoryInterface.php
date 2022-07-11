<?php

namespace App\Repositories\Admins\Coins;

use Illuminate\Support\Collection;

interface CoinsRepositoryInterface
{
    public function getTable(): string;

    public function getCoins(): Collection;

    public function getCoinsList(): Collection;

    public function getLatestCoin(): object;

    public function createCoin(array $resource): int;

    public function updateCoin(array $resource, int $id): int;

    public function deleteCoin(array $resource, array $ids): int;
}
