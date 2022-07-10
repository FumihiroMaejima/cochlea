<?php

namespace App\Repositories\Admins\Images;

use Illuminate\Support\Collection;

interface ImagesRepositoryInterface
{
    public function getTable(): string;

    public function getImages(): Collection;

    public function getByUuid(string $uuid): Collection|null;

    public function getLatestImage(): object;

    public function createImage(array $resource): int;

    public function updateImage(array $resource, int $id): int;

    public function deleteImage(array $resource, array $ids): int;
}
