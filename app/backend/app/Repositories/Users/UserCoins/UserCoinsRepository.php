<?php

namespace App\Repositories\Users\UserCoins;

use App\Exceptions\MyApplicationHttpException;
use App\Exceptions\ExceptionStatusCodeMessages;
use App\Models\Users\UserCoins;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class UserCoinsRepository implements UserCoinsRepositoryInterface
{
    protected UserCoins $model;

    private const NO_DATA_COUNT = 0;
    private const FIRST_DATA_COUNT = 1;

    /**
     * create a new UserCoinsRepository instance.
     *
     * @param UserCoins $model
     * @return void
     */
    public function __construct(UserCoins $model)
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
     * get by user id.
     *
     * @param int $userId user id.
     * @return Collection|null
     * @throws MyApplicationHttpException
     */
    public function getByUserId(int $userId): Collection|null
    {
        $collection = DB::table($this->getTable())
            ->select(['*'])
            ->where(UserCoins::USER_ID, '=', $userId)
            ->where(UserCoins::DELETED_AT, '=', null)
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
     * create UserCoins data.
     *
     * @param int $userId user id.
     * @param array $resource create data
     * @return int
     */
    public function createUserCoins(int $userId, array $resource): int
    {
        return DB::table($this->getTable())->insert($resource);
    }

    /**
     * update UserCoins data.
     *
     * @param int $userId user id.
     * @param array $resource update data
     * @return int
     */
    public function updateUserCoins(int $userId, array $resource): int
    {
        // Query Builderのupdate
        return DB::table($this->getTable())
            // ->whereIn('id', [$id])
            ->where(UserCoins::USER_ID, '=', $userId)
            ->where(UserCoins::DELETED_AT, '=', null)
            ->update($resource);
    }

    /**
     * delete UserCoins data.
     *
     * @param int $userId user id.
     * @param array $resource update data
     * @return int
     */
    public function deleteUserCoins(int $userId, array $resource): int
    {
        // Query Builderのupdate
        return DB::table($this->getTable())
            ->where(UserCoins::USER_ID, '=', $userId)
            ->where(UserCoins::DELETED_AT, '=', null)
            ->update($resource);
    }
}
