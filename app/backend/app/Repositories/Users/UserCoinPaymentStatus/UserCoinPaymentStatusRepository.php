<?php

namespace App\Repositories\Users\UserCoinPaymentStatus;

use App\Exceptions\MyApplicationHttpException;
use App\Exceptions\ExceptionStatusCodeMessages;
use App\Models\Users\UserCoinPaymentStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class UserCoinPaymentStatusRepository implements UserCoinPaymentStatusRepositoryInterface
{
    protected UserCoinPaymentStatus $model;

    private const NO_DATA_COUNT = 0;
    private const FIRST_DATA_COUNT = 1;

    /**
     * create a new UserCoinPaymentStatusRepository instance.
     *
     * @param UserCoinPaymentStatus $model
     * @return void
     */
    public function __construct(UserCoinPaymentStatus $model)
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
        $collection = DB::connection(UserCoinPaymentStatus::setConnectionName($userId))
            ->table($this->getTable())
            ->select(['*'])
            ->where(UserCoinPaymentStatus::USER_ID, '=', $userId)
            ->where(UserCoinPaymentStatus::DELETED_AT, '=', null)
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
     * @param int $userId user id.
     * @param string $orderId order id.
     * @return Collection|null
     * @throws MyApplicationHttpException
     */
    public function getByUserIdAndOrderId(int $userId, string $orderId): Collection|null
    {
        $collection = DB::connection(UserCoinPaymentStatus::setConnectionName($userId))
            ->table($this->getTable())
            ->select(['*'])
            ->where(UserCoinPaymentStatus::USER_ID, '=', $userId)
            ->where(UserCoinPaymentStatus::ORDER_ID, '=', $orderId)
            ->where(UserCoinPaymentStatus::DELETED_AT, '=', null)
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
     * create UserCoinPaymentStatus data.
     *
     * @param int $userId user id.
     * @param array $resource create data
     * @return int
     */
    public function createUserCoinPaymentStatus(int $userId, array $resource): int
    {
        return DB::connection(UserCoinPaymentStatus::setConnectionName($userId))->table($this->getTable())->insert($resource);
    }

    /**
     * update UserCoinPaymentStatus data.
     *
     * @param int $userId user id.
     * @param string $orderId order id.
     * @param array $resource update data
     * @return int
     */
    public function updateUserCoinPaymentStatus(int $userId, string $orderId, array $resource): int
    {
        // Query Builderのupdate
        return DB::connection(UserCoinPaymentStatus::setConnectionName($userId))
            ->table($this->getTable())
            // ->whereIn('id', [$id])
            ->where(UserCoinPaymentStatus::USER_ID, '=', $userId)
            ->where(UserCoinPaymentStatus::ORDER_ID, '=', $orderId)
            ->where(UserCoinPaymentStatus::DELETED_AT, '=', null)
            ->update($resource);
    }

    /**
     * delete UserCoinPaymentStatus data.
     *
     * @param int $userId user id.
     * @param string $orderId order id.
     * @param array $resource update data
     * @return int
     */
    public function deleteUserCoinPaymentStatus(int $userId, string $orderId, array $resource): int
    {
        // Query Builderのupdate
        return DB::connection(UserCoinPaymentStatus::setConnectionName($userId))
            ->table($this->getTable())
            ->where(UserCoinPaymentStatus::USER_ID, '=', $userId)
            ->where(UserCoinPaymentStatus::ORDER_ID, '=', $orderId)
            ->where(UserCoinPaymentStatus::DELETED_AT, '=', null)
            ->update($resource);
    }
}
