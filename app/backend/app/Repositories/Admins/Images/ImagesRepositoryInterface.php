<?php

namespace App\Repositories\Admins\Images;

use Illuminate\Support\Collection;

interface ImagesRepositoryInterface
{
    public function getTable(): string;

    public function getImages(): Collection;

    public function getByUuid(string $uuid, int $version): Collection|null;

    public function getLatestImage(): object;

    public function create(array $resource): int;

    public function update(int $id, array $resource): int;

    public function delete(array $ids, array $resource): int;
}
