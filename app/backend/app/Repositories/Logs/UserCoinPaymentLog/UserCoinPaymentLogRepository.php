<?php

namespace App\Repositories\Logs\UserCoinPaymentLog;

use App\Exceptions\MyApplicationHttpException;
use App\Exceptions\ExceptionStatusCodeMessages;
use App\Models\Logs\UserCoinPaymentLog;
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
     * @return Builder
     */
    public function getQueryBuilder(): Builder
    {
        return DB::connection(UserCoinPaymentLog::setConnectionName())->table($this->getTable());
    }

    /**
     * create partition.
     *
     * @return bool
     * @throws MyApplicationHttpException
     */
    public function createPartition(): bool
    {
        $connection = UserCoinPaymentLog::setConnectionName();
        $table = $this->getTable();

        $date = '2022-04-01 00:00:00';

        $this->getQueryBuilder()->raw('
            ALTER TABLE "${connection}"."${table}"
            PARTITION BY RANGE COLUMNS(created_at) (
                PARTITION p202203 VALUES LESS THAN ($date),
                PARTITION p202204 VALUES LESS THAN ($date),
                PARTITION p202205 VALUES LESS THAN ($date),
                PARTITION p202206 VALUES LESS THAN ($date),
                PARTITION p202207 VALUES LESS THAN ($date)
            );
        ')->getValue();

        return true;
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
        $collection = $this->getQueryBuilder()
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
        $collection = $this->getQueryBuilder()
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
        return $this->getQueryBuilder()->insert($resource);
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
        return $this->getQueryBuilder()
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
        return $this->getQueryBuilder()
            ->where(UserCoinPaymentLog::USER_ID, '=', $userId)
            ->where(UserCoinPaymentLog::ORDER_ID, '=', $orderId)
            ->where(UserCoinPaymentLog::DELETED_AT, '=', null)
            ->update($resource);
    }
}
