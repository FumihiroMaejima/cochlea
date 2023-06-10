<?php

namespace App\Repositories\Users\UserAuthCodes;

use App\Exceptions\MyApplicationHttpException;
use App\Library\Array\ArrayLibrary;
use App\Library\Message\StatusCodeMessages;
use App\Models\Users\UserAuthCodes;
use App\Repositories\Users\BaseUserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Database\Query\Builder;

class UserAuthCodesRepository extends BaseUserRepository implements UserAuthCodesRepositoryInterface
{
    /**
     * create instance.
     *
     * @param UserAuthCodes $model
     * @return void
     */
    public function __construct(UserAuthCodes $model)
    {
        $this->model = $model;
    }

    /**
     * get by user id.
     *
     * @param int $userId user id
     * @param bool $isLock exec lock For Update
     * @return array|null
     * @throws MyApplicationHttpException
     */
    public function getByUserId(int $userId, bool $isLock = false): array|null
    {
        $query = $this->getQueryBuilder($userId)
            ->select(['*'])
            ->where(UserAuthCodes::USER_ID, '=', $userId);

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

        // return $collection;
        return ArrayLibrary::getFirst(ArrayLibrary::toArray($collection->toArray()));
    }

    /**
     * get list by user id.
     *
     * @param int $userId user id
     * @return array
     * @throws MyApplicationHttpException
     */
    public function getListByUserId(int $userId): array
    {
        $query = $this->getQueryBuilder($userId)
            ->select(['*'])
            ->where(UserAuthCodes::USER_ID, '=', $userId);

        // return $query->get();
        return ArrayLibrary::toArray($query->get()->toArray());
    }

    /**
     * get by user id & code.
     *
     * @param int $userId user id
     * @param int $code code
     * @param bool $isLock exec lock For Update
     * @return Collection|null
     * @throws MyApplicationHttpException
     */
    public function getByUserIdAndCode(int $userId, int $code, bool $isLock = false): Collection|null
    {
        $query = $this->getQueryBuilder($userId)
            ->select(['*'])
            ->where(UserAuthCodes::USER_ID, '=', $userId)
            ->where(UserAuthCodes::CODE, '=', $code);

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
     * @param int $authCode auth code
     * @param array $resource update data
     * @return int
     */
    public function update(int $userId, int $authCode, array $resource): int
    {
        // Query Builderのupdate
        return $this->getQueryBuilder($userId)
            // ->whereIn('id', [$id])
            ->where(UserAuthCodes::USER_ID, '=', $userId)
            ->where(UserAuthCodes::CODE, '=', $authCode)
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
            ->where(UserAuthCodes::USER_ID, '=', $userId)
            ->where(UserAuthCodes::CREATED_AT, '=', $createdAt)
            ->update($resource);
    }
}
