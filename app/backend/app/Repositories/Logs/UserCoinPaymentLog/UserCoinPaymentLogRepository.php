<?php

namespace App\Repositories\Logs\UserCoinPaymentLog;

use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Models\Logs\BaseLogDataModel;
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
     * create instance.
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
        return DB::connection(BaseLogDataModel::getLogDatabaseConnection())->table($this->getTable());
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
                StatusCodeMessages::STATUS_500,
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
                StatusCodeMessages::STATUS_500,
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
        return $this->getQueryBuilder()->insert($resource);
    }

    /**
     * update recode.
     *
     * @param int $userId user id
     * @param string $orderId order id
     * @param array $resource update data
     * @return int
     */
    public function update(int $userId, string $orderId, array $resource): int
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
     * delete recode.
     *
     * @param int $userId user id
     * @param string $orderId order id
     * @param array $resource update data
     * @return int
     */
    public function delete(int $userId, string $orderId, array $resource): int
    {
        // Query Builderのupdate
        return $this->getQueryBuilder()
            ->where(UserCoinPaymentLog::USER_ID, '=', $userId)
            ->where(UserCoinPaymentLog::ORDER_ID, '=', $orderId)
            ->where(UserCoinPaymentLog::DELETED_AT, '=', null)
            ->update($resource);
    }
}
