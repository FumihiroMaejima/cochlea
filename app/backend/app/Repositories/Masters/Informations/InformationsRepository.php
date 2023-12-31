<?php

declare(strict_types=1);

namespace App\Repositories\Masters\Informations;

use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Models\Masters\Coins;
use App\Models\Masters\Informations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class InformationsRepository implements InformationsRepositoryInterface
{
    protected Informations $model;

    private const NO_DATA_COUNT = 0;
    private const FIRST_DATA_COUNT = 1;

    /**
     * create instance.
     *
     * @param \App\Models\Masters\Informations $model
     * @return void
     */
    public function __construct(Informations $model)
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
     * get All Record Data.
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
            ->where(Informations::DELETED_AT, '=', null)
            ->get();
    }

    /**
     * get Records as List.
     *
     * @return Collection
     */
    public function getRecordList(): Collection
    {
        // coins
        $coins = $this->getTable();

        // collection
        return DB::table($coins)
            ->select([Informations::ID, Informations::NAME])
            ->where(Informations::DELETED_AT, '=', null)
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
            ->where(Informations::ID, '=', $id)
            ->where(Informations::DELETED_AT, '=', null)
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
            ->where(Informations::ID, '=', $id)
            ->where(Informations::DELETED_AT, '=', null)
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
            ->whereIn(Informations::ID, $ids)
            ->where(Informations::DELETED_AT, '=', null)
            ->get();

        // 存在しない場合
        if ($collection->count() === self::NO_DATA_COUNT) {
            return null;
        }

        if ($isLock) {
            // ロックをかけた状態で再検索
            $collection = DB::table($this->getTable())
                ->select(['*'])
                ->whereIn(Informations::ID, $ids)
                ->where(Informations::DELETED_AT, '=', null)
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
    public function create(array $resource): bool
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
            ->where(Informations::ID, '=', [$id])
            ->where(Informations::DELETED_AT, '=', null)
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
            ->whereIn(Informations::ID, $ids)
            ->where(Informations::DELETED_AT, '=', null)
            ->update($resource);
    }
}
