<?php

namespace App\Repositories\Admins\Images;

use Illuminate\Support\Collection;

interface ImagesRepositoryInterface
{
    public function getTable(): string;

    public function getImages(): Collection;

    public function getLatestImage(): object;

    public function getByIds(array $ids, bool $isLock = false): Collection|null;

    public function getByUuid(string $uuid, bool $isLock = false): Collection|null;

    public function create(array $resource): int;

    public function update(int $id, array $resource): int;

    public function delete(int $id, array $resource): int;
}
