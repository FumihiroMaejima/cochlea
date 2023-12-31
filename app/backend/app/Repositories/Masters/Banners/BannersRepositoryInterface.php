<?php

declare(strict_types=1);

namespace App\Repositories\Masters\Banners;

use Illuminate\Support\Collection;

interface BannersRepositoryInterface
{
    public function getTable(): string;

    public function getRecords(): Collection;

    public function getRecordList(): Collection;

    public function getLatestRecord(): object;

    public function getById(int $id, bool $isLock = false): Collection|null;

    public function getByIds(array $ids, bool $isLock = false): Collection|null;

    public function getByUuid(string $uuid, bool $isLock = false): Collection|null;

    public function getByUuids(array $uuids, bool $isLock = false): Collection|null;

    public function create(array $resource): bool;

    public function update(int $id, array $resource): int;

    public function delete(array $ids, array $resource): int;
}
