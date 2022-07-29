<?php

namespace App\Repositories\Logs\UserCoinPaymentLog;

use Illuminate\Support\Collection;
use Illuminate\Database\Query\Builder;

interface UserCoinPaymentLogRepositoryInterface
{
    public function getTable(): string;

    public function getQueryBuilder(): Builder;

    public function createPartition(): bool;

    public function getByUserId(int $userId): Collection|null;

    public function getByUserIdAndOrderId(int $userId, string $orderId): Collection|null;

    public function createUserCoinPaymentLog(int $userId, array $resource): int;

    public function updateUserCoinPaymentLog(int $userId, string $orderId, array $resource): int;

    public function deleteUserCoinPaymentLog(int $userId, string $orderId, array $resource): int;
}
