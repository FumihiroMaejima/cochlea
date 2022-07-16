<?php

namespace App\Repositories\Admins\Coins;

use App\Exceptions\MyApplicationHttpException;
use App\Exceptions\ExceptionStatusCodeMessages;
use App\Models\Masters\Coins;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class CoinsRepository implements CoinsRepositoryInterface
{
    protected Coins $model;

    private const NO_DATA_COUNT = 0;
    private const FIRST_DATA_COUNT = 1;

    /**
     * create a new CoinsRepository instance.
     *
     * @param \App\Models\Masters\Coins $model
     * @return void
     */
    public function __construct(Coins $model)
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
    public function getCoins(): Collection
    {
        // coins
        $coins = $this->getTable();

        // collection
        return DB::table($coins)
            ->select(['*'])
            ->where(Coins::DELETED_AT, '=', null)
            ->get();
    }

    /**
     * get Coins as List.
     *
     * @return Collection
     */
    public function getCoinsList(): Collection
    {
        // coins
        $coins = $this->getTable();

        // collection
        return DB::table($coins)
            ->select([Coins::ID, Coins::NAME])
            ->where(Coins::DELETED_AT, '=', null)
            ->get();
    }

    /**
     * get Latest Coin data.
     *
     * @return \Illuminate\Database\Eloquent\Model|object|static|null
     */
    public function getLatestCoin(): object
    {
        return DB::table($this->getTable())
            ->latest()
            ->first();
    }

    /**
     * get by table id.
     *
     * @param int $id table id.
     * @return Collection|null
     * @throws MyApplicationHttpException
     */
    public function getById(int $id): Collection|null
    {
        $collection = DB::table($this->getTable())
            ->select(['*'])
            ->where(Coins::ID, '=', $id)
            ->where(Coins::DELETED_AT, '=', null)
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
     * create Coin data.
     *
     * @param array $resource create data
     * @return int
     */
    public function createCoin(array $resource): int
    {
        return DB::table($this->getTable())->insert($resource);
    }

    /**
     * update Coin data.
     *
     * @param array $id id of record
     * @param array $resource update data
     * @return int
     */
    public function updateCoin(int $id, array $resource): int
    {
        // Query Builderのupdate
        return DB::table($this->getTable())
            // ->whereIn('id', [$id])
            ->where(Coins::ID, '=', [$id])
            ->where(Coins::DELETED_AT, '=', null)
            ->update($resource);
    }

    /**
     * delete Coins data.
     *
     * @param array $ids id of records
     * @param array $resource update data
     * @return int
     */
    public function deleteCoin(array $ids, array $resource): int
    {
        // Query Builderのupdate
        return DB::table($this->getTable())
            ->whereIn(Coins::ID, $ids)
            ->where(Coins::DELETED_AT, '=', null)
            ->update($resource);
    }
}
