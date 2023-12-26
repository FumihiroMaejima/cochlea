<?php

declare(strict_types=1);

namespace App\Repositories\Users\UserCoinPaymentStatus;

use Illuminate\Support\Collection;
use Illuminate\Database\Query\Builder;

interface UserCoinPaymentStatusRepositoryInterface
{
    public function getByUserId(int $userId): Collection|null;

    public function getByUserIdAndOrderId(int $userId, string $orderId, bool $isLock = false): Collection|null;

    public function create(int $userId, array $resource): bool;

    public function update(int $userId, string $orderId, array $resource): int;

    public function delete(int $userId, string $orderId, array $resource): int;
}
