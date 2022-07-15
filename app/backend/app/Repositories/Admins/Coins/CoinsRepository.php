<?php

namespace App\Repositories\Admins\Coins;

use App\Models\Masters\Coins;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class CoinsRepository implements CoinsRepositoryInterface
{
    protected Coins $model;

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
     * @param array $resource update data
     * @param array $id id of record
     * @return int
     */
    public function updateCoin(array $resource, int $id): int
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
     * @param array $resource update data
     * @param array $ids id of records
     * @return int
     */
    public function deleteCoin(array $resource, array $ids): int
    {
        // Query Builderのupdate
        return DB::table($this->getTable())
            ->whereIn(Coins::ID, $ids)
            ->where(Coins::DELETED_AT, '=', null)
            ->update($resource);
    }
}
