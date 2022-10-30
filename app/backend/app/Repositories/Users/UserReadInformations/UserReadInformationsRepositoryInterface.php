<?php

namespace App\Repositories\Users\UserReadInformations;

use Illuminate\Support\Collection;
use Illuminate\Database\Query\Builder;

interface UserReadInformationsRepositoryInterface
{
    public function getTable(int $userId): string;

    public function getQueryBuilder(int $userId): Builder;

    public function getByUserId(int $userId, bool $isLock = false): Collection|null;

    public function getByUserIdAndInformationId(int $userId, int $informationId, bool $isLock = false): Collection|null;

    public function create(int $userId, array $resource): int;

    public function update(int $userId, array $resource): int;

    public function delete(int $userId, array $resource): int;
}
