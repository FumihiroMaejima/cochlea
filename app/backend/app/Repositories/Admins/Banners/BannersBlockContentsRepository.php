<?php

namespace App\Repositories\Admins\Banners;

use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Models\Masters\BannerBlockContents;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class BannersBlockContentsRepository implements BannersBlockContentsRepositoryInterface
{
    protected BannerBlockContents $model;

    private const NO_DATA_COUNT = 0;
    private const FIRST_DATA_COUNT = 1;

    /**
     * create instance.
     *
     * @param \App\Models\Masters\BannerBlockContents $model
     * @return void
     */
    public function __construct(BannerBlockContents $model)
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
            ->where(BannerBlockContents::DELETED_AT, '=', null)
            ->get();
    }

    /**
     * get BannerBlockContents as List.
     *
     * @return Collection
     */
    public function getRecordList(): Collection
    {
        // coins
        $coins = $this->getTable();

        // collection
        return DB::table($coins)
            ->select([BannerBlockContents::ID, BannerBlockContents::BANNER_BLOCK_ID, BannerBlockContents::BANNER_ID])
            ->where(BannerBlockContents::DELETED_AT, '=', null)
            ->get();
    }

    /**
     * get Latest Coin data.
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
            ->where(BannerBlockContents::ID, '=', $id)
            ->where(BannerBlockContents::DELETED_AT, '=', null)
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
            ->where(BannerBlockContents::ID, '=', $id)
            ->where(BannerBlockContents::DELETED_AT, '=', null)
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
            ->whereIn(BannerBlockContents::ID, $ids)
            ->where(BannerBlockContents::DELETED_AT, '=', null)
            ->get();

        // 存在しない場合
        if ($collection->count() === self::NO_DATA_COUNT) {
            return null;
        }

        if ($isLock) {
            // ロックをかけた状態で再検索
            $collection = DB::table($this->getTable())
                ->select(['*'])
                ->whereIn(BannerBlockContents::ID, $ids)
                ->where(BannerBlockContents::DELETED_AT, '=', null)
                ->lockForUpdate()
                ->get();
        }

        return $collection;
    }

    /**
     * get by record id.
     *
     * @param int $blockId block id
     * @param bool $isLock exec lock For Update
     * @param bool $isLisSkipDeplicateock whicherver skip deplicate rocords
     * @return Collection|null
     * @throws MyApplicationHttpException
     */
    public function getByBlockId(int $blockId, bool $isLock = false, bool $isSkipDeplicate = true): Collection|null
    {
        $collection = DB::table($this->getTable())
            ->select(['*'])
            ->where(BannerBlockContents::BANNER_BLOCK_ID, '=', $blockId)
            ->where(BannerBlockContents::DELETED_AT, '=', null)
            ->get();

        // 存在しない場合
        if ($collection->count() === self::NO_DATA_COUNT) {
            return null;
        }

        // 複数ある場合
        if (($collection->count() > self::FIRST_DATA_COUNT) && !$isSkipDeplicate) {
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
            ->where(BannerBlockContents::BANNER_BLOCK_ID, '=', $blockId)
            ->where(BannerBlockContents::DELETED_AT, '=', null)
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
            ->where(BannerBlockContents::ID, '=', [$id])
            ->where(BannerBlockContents::DELETED_AT, '=', null)
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
            ->whereIn(BannerBlockContents::ID, $ids)
            ->where(BannerBlockContents::DELETED_AT, '=', null)
            ->update($resource);
    }
}
