<?php

namespace App\Repositories\Masters\Images;

use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Models\Masters\Images;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class ImagesRepository implements ImagesRepositoryInterface
{
    protected Images $model;

    private const NO_DATA_COUNT = 0;
    private const FIRST_DATA_COUNT = 1;

    /**
     * create instance.
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
     * get by ids.
     *
     * @param array $ids rocord ids
     * @param bool $isLock exec lock For Update
     * @return Collection|null
     * @throws MyApplicationHttpException
     */
    public function getByIds(array $ids, bool $isLock = false): Collection|null
    {
        $collection = DB::table($this->getTable())
            ->select(['*'])
            ->whereIn(Images::ID, $ids)
            ->where(Images::DELETED_AT, '=', null)
            ->get();

        // 存在しない場合
        if ($collection->count() === self::NO_DATA_COUNT) {
            return null;
        }

        if ($isLock) {
            // ロックをかけた状態で再検索
            $collection = DB::table($this->getTable())
            ->lockForUpdate()
            ->select(['*'])
            ->whereIn(Images::ID, $ids)
            ->where(Images::DELETED_AT, '=', null)
            ->get();
        }

        return $collection;
    }

    /**
     * get Image by uuid.
     *
     * @param string $uuid
     * @param bool $isLock exec lock For Update
     * @return Collection|null
     * @throws MyApplicationHttpException
     */
    public function getByUuid(string $uuid, bool $isLock = false): Collection|null
    {
        $collection = DB::table($this->getTable())
            ->select(['*'])
            ->where(Images::UUID, '=', $uuid)
            ->where(Images::DELETED_AT, '=', null)
            ->get();

        // 存在しない場合
        if ($collection->count() === self::NO_DATA_COUNT) {
            return null;
        }

        // 複数ある場合
        if ($collection->count() > self::FIRST_DATA_COUNT) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_500,
                'has deplicate collections,'
            );
        }

        if ($isLock) {
            // ロックをかけた状態で再検索
            $collection = DB::table($this->getTable())
                ->select(['*'])
                ->where(Images::UUID, '=', $uuid)
                ->where(Images::DELETED_AT, '=', null)
                ->lockForUpdate()
                ->get();
        }

        return $collection;
    }

    /**
     * create recode.
     *
     * @param array $resource create data
     * @return int
     */
    public function create(array $resource): int
    {
        return DB::table($this->getTable())->insert($resource);
    }

    /**
     * update recode.
     *
     * @param array $id id of record
     * @param array $resource update data
     * @return int
     */
    public function update(int $id, array $resource): int
    {
        // Query Builderのupdate
        return DB::table($this->getTable())
            // ->whereIn('id', [$id])
            ->where(Images::ID, '=', $id)
            ->where(Images::DELETED_AT, '=', null)
            ->update($resource);
    }

    /**
     * delete recode.
     *
     * @param int $id id of record
     * @param array $resource update data
     * @return int
     */
    public function delete(int $id, array $resource): int
    {
        // Query Builderのupdate
        return DB::table($this->getTable())
            ->where(Images::ID, '=', $id)
            ->where(Images::DELETED_AT, '=', null)
            ->update($resource);
    }
}
