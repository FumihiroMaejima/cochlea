<?php

namespace App\Repositories\Logs\UserCoinPaymentLog;

use Illuminate\Support\Collection;
use Illuminate\Database\Query\Builder;

interface UserCoinPaymentLogRepositoryInterface
{
    public function getTable(): string;

    public function getQueryBuilder(): Builder;

    public function getByUserId(int $userId): Collection|null;

    public function getByUserIdAndOrderId(int $userId, string $orderId): Collection|null;

    public function create(int $userId, array $resource): int;

    public function update(int $userId, string $orderId, array $resource): int;

    public function delete(int $userId, string $orderId, array $resource): int;
}
