<?php

namespace App\Repositories\Users\UserServiceTerms;

use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Models\Users\UserServiceTerms;
use App\Repositories\Users\BaseUserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Database\Query\Builder;

class UserServiceTermsRepository extends BaseUserRepository implements UserServiceTermsRepositoryInterface
{
    /**
     * create instance.
     *
     * @param UserServiceTerms $model
     * @return void
     */
    public function __construct(UserServiceTerms $model)
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
            ->where(UserServiceTerms::USER_ID, '=', $userId)
            ->where(UserServiceTerms::DELETED_AT, '=', null);

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
     * get by user id & master id.
     *
     * @param int $userId user id
     * @param int $serviceTermId service term id
     * @param bool $isLock exec lock For Update
     * @return Collection|null
     * @throws MyApplicationHttpException
     */
    public function getByUserIdAndServiceTermId(int $userId, int $serviceTermId, bool $isLock = false): Collection|null
    {
        $query = $this->getQueryBuilder($userId)
            ->select(['*'])
            ->where(UserServiceTerms::USER_ID, '=', $userId)
            ->where(UserServiceTerms::SERVICE_TERM_ID, '=', $serviceTermId)
            ->where(UserServiceTerms::DELETED_AT, '=', null);

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
            ->where(UserServiceTerms::USER_ID, '=', $userId)
            ->where(UserServiceTerms::DELETED_AT, '=', null)
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
            ->where(UserServiceTerms::USER_ID, '=', $userId)
            ->where(UserServiceTerms::DELETED_AT, '=', null)
            ->update($resource);
    }
}
