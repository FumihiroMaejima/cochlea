<?php

namespace App\Repositories\Admins\Images;

use Illuminate\Support\Collection;

interface ImagesRepositoryInterface
{
    public function getTable(): string;

    public function getImages(): Collection;

    public function getByUuid(string $uuid, int $version): Collection|null;

    public function getLatestImage(): object;

    public function createImage(array $resource): int;

    public function updateImage(int $id, array $resource): int;

    public function deleteImage(array $ids, array $resource): int;
}
