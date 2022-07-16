<?php

namespace App\Repositories\Users\UserCoinPaymentStatus;

use Illuminate\Support\Collection;

interface UserCoinPaymentStatusRepositoryInterface
{
    public function getTable(int $userId): string;

    public function getByUserId(int $userId): Collection|null;

    public function getByUserIdAndOrderId(int $userId, string $orderId): Collection|null;

    public function createUserCoinPaymentStatus(int $userId, array $resource): int;

    public function updateUserCoinPaymentStatus(int $userId, string $orderId, array $resource): int;

    public function deleteUserCoinPaymentStatus(int $userId, string $orderId, array $resource): int;
}
