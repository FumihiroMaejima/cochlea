<?php

declare(strict_types=1);

namespace App\Repositories\Users\UserAuthCodes;

use Illuminate\Support\Collection;
use Illuminate\Database\Query\Builder;

interface UserAuthCodesRepositoryInterface
{
    public function getByUserId(int $userId, bool $isLock = false): array|null;

    public function getListByUserId(int $userId): array;

    public function getByUserIdAndCode(int $userId, int $code, bool $isLock = false): Collection|null;

    public function create(int $userId, array $resource): bool;

    public function update(int $userId, int $authCode, array $resource): int;

    public function delete(int $userId, string $createdAt, array $resource): int;
}
