<?php

namespace App\Repositories\Admins\Images;

use App\Exceptions\MyApplicationHttpException;
use App\Exceptions\ExceptionStatusCodeMessages;
use App\Models\Masters\Images;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class ImagesRepository implements ImagesRepositoryInterface
{
    protected Images $model;

    private const NO_DATA_COUNT = 0;
    private const FIRST_DATA_COUNT = 1;

    /**
     * create a new ImagesRepository instance.
     *
     * @param Images $model
     * @return void
     */
    public function __construct(Images $model)
    {
        $this->model = $model;
    }

    /**
     * get Model Table Name in This Repository.
     *
     * @return string
     */
    public function getTable(): string
    {
        return $this->model->getTable();
    }

    /**
     * get All Image Data.
     *
     * @return Collection
     */
    public function getImages(): Collection
    {
        // collection
        return DB::table($this->getTable())
            ->select(['*'])
            ->where(Images::DELETED_AT, '=', null)
            ->get();
    }

    /**
     * get Image by uuid.
     *
     * @param string $uuid
     * @param int $version
     * @return Collection|null
     * @throws MyApplicationHttpException
     */
    public function getByUuid(string $uuid, int $version): Collection|null
    {
        $collection = DB::table($this->getTable())
            ->select(['*'])
            ->where(Images::UUID, '=', $uuid)
            ->where(Images::VERSION, '=', $version)
            ->where(Images::DELETED_AT, '=', null)
            ->get();

        // 存在しない場合
        if ($collection->count() === self::NO_DATA_COUNT) {
            return null;
        }

        // 複数ある場合
        if ($collection->count() > self::FIRST_DATA_COUNT) {
            throw new MyApplicationHttpException(
                ExceptionStatusCodeMessages::STATUS_CODE_500,
                'has deplicate collections,'
            );
        }

        return $collection;
    }

    /**
     * get Latest Image data.
     *
     * @return \Illuminate\Database\Eloquent\Model|object|static|null
     */
    public function getLatestImage(): object
    {
        return DB::table($this->getTable())
            ->latest()
            ->first();
    }

    /**
     * create Image data.
     *
     * @param array $resource create data
     * @return int
     */
    public function createImage(array $resource): int
    {
        return DB::table($this->getTable())->insert($resource);
    }

    /**
     * update Image data.
     *
     * @param array $id id of record
     * @param array $resource update data
     * @return int
     */
    public function updateImage(int $id, array $resource): int
    {
        // Query Builderのupdate
        return DB::table($this->getTable())
            // ->whereIn('id', [$id])
            ->where(Images::ID, '=', [$id])
            ->where(Images::DELETED_AT, '=', null)
            ->update($resource);
    }

    /**
     * delete Images data.
     *
     * @param array $ids id of records
     * @param array $resource update data
     * @return int
     */
    public function deleteImage(array $ids, array $resource): int
    {
        // Query Builderのupdate
        return DB::table($this->getTable())
            ->whereIn(Images::ID, $ids)
            ->where(Images::DELETED_AT, '=', null)
            ->update($resource);
    }
}
