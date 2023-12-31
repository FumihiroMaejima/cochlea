<?php

declare(strict_types=1);

namespace App\Repositories\Masters\Images;

use Illuminate\Support\Collection;

interface ImagesRepositoryInterface
{
    public function getTable(): string;

    public function getImages(): Collection;

    public function getLatestImage(): object;

    public function getByIds(array $ids, bool $isLock = false): Collection|null;

    public function getByUuid(string $uuid, bool $isLock = false): Collection|null;

    public function create(array $resource): bool;

    public function update(int $id, array $resource): int;

    public function delete(int $id, array $resource): int;
}
