<?php

namespace App\Repositories\Masters\Banners;

use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Models\Masters\Banners;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class BannersRepository implements BannersRepositoryInterface
{
    protected Banners $model;

    private const NO_DATA_COUNT = 0;
    private const FIRST_DATA_COUNT = 1;

    /**
     * create instance.
     *
     * @param \App\Models\Masters\Banners $model
     * @return void
     */
    public function __construct(Banners $model)
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
     * get All Coin Data.
     *
     * @return Collection
     */
    public function getRecords(): Collection
    {
        // coins
        $coins = $this->getTable();

        // collection
        return DB::table($coins)
            ->select(['*'])
            ->where(Banners::DELETED_AT, '=', null)
            ->get();
    }

    /**
     * get Banners as List.
     *
     * @return Collection
     */
    public function getRecordList(): Collection
    {
        // coins
        $coins = $this->getTable();

        // collection
        return DB::table($coins)
            ->select([Banners::ID, Banners::NAME])
            ->where(Banners::DELETED_AT, '=', null)
            ->get();
    }

    /**
     * get Latest Record data.
     *
     * @return \Illuminate\Database\Eloquent\Model|object|static|null
     */
    public function getLatestRecord(): object
    {
        return DB::table($this->getTable())
            ->latest()
            ->first();
    }

    /**
     * get by record id.
     *
     * @param int $id coin id
     * @param bool $isLock exec lock For Update
     * @return Collection|null
     * @throws MyApplicationHttpException
     */
    public function getById(int $id, bool $isLock = false): Collection|null
    {
        $collection = DB::table($this->getTable())
            ->select(['*'])
            ->where(Banners::ID, '=', $id)
            ->where(Banners::DELETED_AT, '=', null)
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
            ->lockForUpdate()
            ->select(['*'])
            ->where(Banners::ID, '=', $id)
            ->where(Banners::DELETED_AT, '=', null)
            ->get();
        }

        return $collection;
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
            ->whereIn(Banners::ID, $ids)
            ->where(Banners::DELETED_AT, '=', null)
            ->get();

        // 存在しない場合
        if ($collection->count() === self::NO_DATA_COUNT) {
            return null;
        }

        if ($isLock) {
            // ロックをかけた状態で再検索
            $collection = DB::table($this->getTable())
                ->select(['*'])
                ->whereIn(Banners::ID, $ids)
                ->where(Banners::DELETED_AT, '=', null)
                ->lockForUpdate()
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
            ->where(Banners::UUID, '=', $uuid)
            ->where(Banners::DELETED_AT, '=', null)
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
                ->where(Banners::UUID, '=', $uuid)
                ->where(Banners::DELETED_AT, '=', null)
                ->lockForUpdate()
                ->get();
        }

        return $collection;
    }

    /**
     * get by uuids.
     *
     * @param array $ids rocord uuid list
     * @param bool $isLock exec lock For Update
     * @return Collection|null
     * @throws MyApplicationHttpException
     */
    public function getByUuids(array $uuids, bool $isLock = false): Collection|null
    {
        $collection = DB::table($this->getTable())
            ->select(['*'])
            ->whereIn(Banners::UUID, $uuids)
            ->where(Banners::DELETED_AT, '=', null)
            ->get();

        // 存在しない場合
        if ($collection->count() === self::NO_DATA_COUNT) {
            return null;
        }

        if ($isLock) {
            // ロックをかけた状態で再検索
            $collection = DB::table($this->getTable())
                ->select(['*'])
                ->whereIn(Banners::UUID, $uuids)
                ->where(Banners::DELETED_AT, '=', null)
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
            ->where(Banners::ID, '=', [$id])
            ->where(Banners::DELETED_AT, '=', null)
            ->update($resource);
    }

    /**
     * delete recode.
     *
     * @param array $ids id of records
     * @param array $resource update data
     * @return int
     */
    public function delete(array $ids, array $resource): int
    {
        // Query Builderのupdate
        return DB::table($this->getTable())
            ->whereIn(Banners::ID, $ids)
            ->where(Banners::DELETED_AT, '=', null)
            ->update($resource);
    }
}
