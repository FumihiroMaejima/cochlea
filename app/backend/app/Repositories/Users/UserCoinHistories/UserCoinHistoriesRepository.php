<?php

namespace App\Repositories\Users\UserCoinHistories;

use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Models\Users\UserCoinHistories;
use App\Models\Users\UserCoins;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Database\Query\Builder;

class UserCoinHistoriesRepository implements UserCoinHistoriesRepositoryInterface
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
    public function __construct(UserCoinHistories $model)
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
        return $this->model->getTable() . UserCoinHistories::getShardId($userId);
    }

    /**
     * get query builder by user id.
     *
     * @param int $userId user id
     * @return Builder
     */
    public function getQueryBuilder(int $userId): Builder
    {
        return DB::connection(UserCoinHistories::getConnectionNameByUserId($userId))->table($this->getTable($userId));
    }

    /**
     * get by user id.
     *
     * @param int $userId user id
     * @param bool $isLock exec lock For Update
     * @return Collection|null
     * @throws MyApplicationHttpException
     */
    public function getByUserId(int $userId, bool $isLock = false): Collection|null
    {
        $query = $this->getQueryBuilder($userId)
            ->select(['*'])
            ->where(UserCoinHistories::USER_ID, '=', $userId)
            ->where(UserCoinHistories::DELETED_AT, '=', null);

        if ($isLock) {
            // ロックをかけた状態で再検索
            $query = $query->lockForUpdate();
        }

        $collection = $query->get();

        // 存在しない場合
        if ($collection->count() === self::NO_DATA_COUNT) {
            return null;
        }

        // 複数ある場合
        if ($collection->count() > self::FIRST_DATA_COUNT) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_CODE_500,
                'has deplicate collections,'
            );
        }

        return $collection;
    }

    /**
     * create record.
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
     * @param string $createdAt recode created date time
     * @param array $resource update data
     * @return int
     */
    public function update(int $userId, string $createdAt, array $resource): int
    {
        // Query Builderのupdate
        return $this->getQueryBuilder($userId)
            // ->whereIn('id', [$id])
            ->where(UserCoinHistories::USER_ID, '=', $userId)
            ->where(UserCoinHistories::CREATED_AT, '=', $createdAt)
            ->where(UserCoinHistories::DELETED_AT, '=', null)
            ->update($resource);
    }

    /**
     * delete record.
     *
     * @param int $userId user id
     * @param string $createdAt recode created date time
     * @param array $resource update data
     * @return int
     */
    public function delete(int $userId, string $createdAt, array $resource): int
    {
        // Query Builderのupdate
        return $this->getQueryBuilder($userId)
            ->where(UserCoinHistories::USER_ID, '=', $userId)
            ->where(UserCoinHistories::CREATED_AT, '=', $createdAt)
            ->where(UserCoinHistories::DELETED_AT, '=', null)
            ->update($resource);
    }
}
