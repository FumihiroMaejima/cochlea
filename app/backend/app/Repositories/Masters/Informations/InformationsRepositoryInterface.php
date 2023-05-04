<?php

namespace App\Repositories\Masters\Informations;

use Illuminate\Support\Collection;

interface InformationsRepositoryInterface
{
    public function getTable(): string;

    public function getRecords(): Collection;

    public function getRecordList(): Collection;

    public function getLatestRecord(): object;

    public function getById(int $id, bool $isLock = false): Collection|null;

    public function getByIds(array $ids, bool $isLock = false): Collection|null;

    public function create(array $resource): int;

    public function update(int $id, array $resource): int;

    public function delete(array $ids, array $resource): int;
}