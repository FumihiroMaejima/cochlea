<?php

declare(strict_types=1);

namespace App\Repositories\Masters\Contacts;

use Illuminate\Support\Collection;

interface ContactsRepositoryInterface
{
    public function getTable(): string;

    public function getRecords(): Collection;

    public function getRecordList(): Collection;

    public function getLatestRecord(): object;

    public function getById(int $id, bool $isLock = false): Collection|null;

    public function getByIds(array $ids, bool $isLock = false): Collection|null;

    public function create(array $resource): bool;

    public function update(int $id, array $resource): int;

    public function delete(array $ids, array $resource): int;
}
