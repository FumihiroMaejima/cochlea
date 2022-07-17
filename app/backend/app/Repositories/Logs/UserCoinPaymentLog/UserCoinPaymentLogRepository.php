<?php

namespace App\Repositories\Logs\UserCoinPaymentLog;

use App\Exceptions\MyApplicationHttpException;
use App\Exceptions\ExceptionStatusCodeMessages;
use App\Models\Logs\UserCoinPaymentLog;
use App\Models\Users\UserCoinPaymentStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Database\Query\Builder;

class UserCoinPaymentLogRepository implements UserCoinPaymentLogRepositoryInterface
{
    protected UserCoinPaymentLog $model;

    private const NO_DATA_COUNT = 0;
    private const FIRST_DATA_COUNT = 1;

    /**
     * create a new UserCoinPaymentStatusRepository instance.
     *
     * @param UserCoinPaymentLog $model
     * @return void
     */
    public function __construct(UserCoinPaymentLog $model)
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
     * get query builder by user id
     *
     * @param int $userId user id
     * @return Builder
     */
    public function getQueryBuilder(int $userId): Builder
    {
        return DB::connection(UserCoinPaymentLog::setConnectionName($userId))->table($this->getTable());
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
            ->where(UserCoinPaymentLog::USER_ID, '=', $userId)
            ->where(UserCoinPaymentLog::DELETED_AT, '=', null)
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
     * get by user id & order id.
     *
     * @param int $userId user id
     * @param string $orderId order id
     * @return Collection|null
     * @throws MyApplicationHttpException
     */
    public function getByUserIdAndOrderId(int $userId, string $orderId): Collection|null
    {
        $collection = $this->getQueryBuilder($userId)
            ->select(['*'])
            ->where(UserCoinPaymentLog::USER_ID, '=', $userId)
            ->where(UserCoinPaymentLog::ORDER_ID, '=', $orderId)
            ->where(UserCoinPaymentLog::DELETED_AT, '=', null)
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
     * create UserCoinPaymentLog data.
     *
     * @param int $userId user id
     * @param array $resource create data
     * @return int
     */
    public function createUserCoinPaymentLog(int $userId, array $resource): int
    {
        return $this->getQueryBuilder($userId)->insert($resource);
    }

    /**
     * update UserCoinPaymentLog data.
     *
     * @param int $userId user id
     * @param string $orderId order id
     * @param array $resource update data
     * @return int
     */
    public function updateUserCoinPaymentLog(int $userId, string $orderId, array $resource): int
    {
        // Query Builderのupdate
        return $this->getQueryBuilder($userId)
            // ->whereIn('id', [$id])
            ->where(UserCoinPaymentLog::USER_ID, '=', $userId)
            ->where(UserCoinPaymentLog::ORDER_ID, '=', $orderId)
            ->where(UserCoinPaymentLog::DELETED_AT, '=', null)
            ->update($resource);
    }

    /**
     * delete UserCoinPaymentLog data.
     *
     * @param int $userId user id
     * @param string $orderId order id
     * @param array $resource update data
     * @return int
     */
    public function deleteUserCoinPaymentLog(int $userId, string $orderId, array $resource): int
    {
        // Query Builderのupdate
        return $this->getQueryBuilder($userId)
            ->where(UserCoinPaymentLog::USER_ID, '=', $userId)
            ->where(UserCoinPaymentLog::ORDER_ID, '=', $orderId)
            ->where(UserCoinPaymentLog::DELETED_AT, '=', null)
            ->update($resource);
    }
}
