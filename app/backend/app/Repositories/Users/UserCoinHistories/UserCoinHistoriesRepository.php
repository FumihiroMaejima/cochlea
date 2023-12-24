<?php

declare(strict_types=1);

namespace App\Repositories\Users\UserCoinHistories;

use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Models\Users\UserCoinHistories;
use App\Models\Users\UserCoins;
use App\Repositories\Users\BaseUserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Database\Query\Builder;

class UserCoinHistoriesRepository extends BaseUserRepository implements UserCoinHistoriesRepositoryInterface
{
    /**
     * create instance.
     *
     * @param UserCoinHistories $model
     * @return void
     */
    public function __construct(UserCoinHistories $model)
    {
        $this->model = $model;
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
                StatusCodeMessages::STATUS_500,
                'has deplicate collections,'
            );
        }

        return $collection;
    }

    /**
     * get list by user id.
     *
     * @param int $userId user id
     * @return Collection
     * @throws MyApplicationHttpException
     */
    public function getListByUserId(int $userId): Collection
    {
        $query = $this->getQueryBuilder($userId)
            ->select(['*'])
            ->where(UserCoinHistories::USER_ID, '=', $userId)
            ->where(UserCoinHistories::DELETED_AT, '=', null);

        return $query->get();
    }

    /**
     * get by user id & uuid.
     *
     * @param int $userId user id
     * @param string $uuid uuid
     * @param bool $isLock exec lock For Update
     * @return Collection|null
     * @throws MyApplicationHttpException
     */
    public function getByUserIdAndUuId(int $userId, string $uuid, bool $isLock = false): Collection|null
    {
        $query = $this->getQueryBuilder($userId)
            ->select(['*'])
            ->where(UserCoinHistories::USER_ID, '=', $userId)
            ->where(UserCoinHistories::UUID, '=', $uuid)
            ->where(UserCoinHistories::DELETED_AT, '=', null);

        if ($isLock) {
            // ロックをかけた状態で検索
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
                StatusCodeMessages::STATUS_500,
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
     * @return bool
     */
    public function create(int $userId, array $resource): bool
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
