<?php

namespace App\Repositories\Users\UserCoinPaymentStatus;

use Illuminate\Support\Collection;

interface UserCoinPaymentStatusRepositoryInterface
{
    public function getTable(): string;

    public function getByUserId(int $userId): Collection|null;

    public function getByUserIdAndOrderId(int $userId, string $orderId): Collection|null;

    public function createUserCoinPaymentStatus(array $resource): int;

    public function updateUserCoinPaymentStatus(array $resource, int $userId, string $orderId): int;

    public function deleteUserCoinPaymentStatus(array $resource, int $userId, string $orderId): int;
}
