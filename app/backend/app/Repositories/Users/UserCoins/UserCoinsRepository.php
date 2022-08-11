<?php

namespace App\Repositories\Users\UserCoins;

use App\Exceptions\MyApplicationHttpException;
use App\Exceptions\ExceptionStatusCodeMessages;
use App\Models\Users\UserCoins;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Database\Query\Builder;

class UserCoinsRepository implements UserCoinsRepositoryInterface
{
    protected UserCoins $model;

    private const NO_DATA_COUNT = 0;
    private const FIRST_DATA_COUNT = 1;

    /**
     * create instance.
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
     * @param int $userId user id
     * @return string
     */
    public function getTable(int $userId): string
    {
        return $this->model->getTable() . UserCoins::getShardId($userId);
    }

    /**
     * get query builder by user id.
     *
     * @param int $userId user id
     * @return Builder
     */
    public function getQueryBuilder(int $userId): Builder
    {
        return DB::connection(UserCoins::getConnectionNameByUserId($userId))->table($this->getTable($userId));
    }

    /**
     * get by user id.
     *
     * @param int $userId user id
     * @return Collection|null
     * @throws MyApplicationHttpException
     */
    public function getByUserId(int $userId): Collection|null
    {
        $collection = $this->getQueryBuilder($userId)
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
     * create recode.
     *
     * @param int $userId user id
     * @param array $resource create data
     * @return int
     */
    public function create(int $userId, array $resource): int
    {
        return $this->getQueryBuilder($userId)->insert($resource);
    }

    /**
     * update record.
     *
     * @param int $userId user id
     * @param array $resource update data
     * @return int
     */
    public function update(int $userId, array $resource): int
    {
        // Query Builderのupdate
        return $this->getQueryBuilder($userId)
            // ->whereIn('id', [$id])
            ->where(UserCoins::USER_ID, '=', $userId)
            ->where(UserCoins::DELETED_AT, '=', null)
            ->update($resource);
    }

    /**
     * delete record.
     *
     * @param int $userId user id
     * @param array $resource update data
     * @return int
     */
    public function delete(int $userId, array $resource): int
    {
        // Query Builderのupdate
        return $this->getQueryBuilder($userId)
            ->where(UserCoins::USER_ID, '=', $userId)
            ->where(UserCoins::DELETED_AT, '=', null)
            ->update($resource);
    }
}
