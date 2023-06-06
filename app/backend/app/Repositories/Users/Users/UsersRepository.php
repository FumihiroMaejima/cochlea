<?php

namespace App\Repositories\Users\Users;

use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Models\User;
use App\Repositories\Users\BaseUserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class UsersRepository extends BaseUserRepository implements UsersRepositoryInterface
{
    /**
     * create instance.
     *
     * @param Users $model
     * @return void
     */
    public function __construct(User $model)
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
     * @param int $userId user id
     * @param bool $isLock exec lock For Update
     * @return Collection|null
     * @throws MyApplicationHttpException
     */
    public function getByUserId(int $userId, bool $isLock = false): Collection|null
    {
        $query = DB::table($this->getTable())
            ->select(['*'])
            ->where(User::ID, '=', $userId)
            ->where(User::DELETED_AT, '=', null);

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
        $query = DB::table($this->getTable())
            ->select(['*'])
            ->where(User::ID, '=', $userId);

        return $query->get();
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
        return DB::table($this->getTable())->insert($resource);
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
        return DB::table($this->getTable())
            ->where(User::ID, '=', $userId)
            ->where(User::CREATED_AT, '=', $createdAt)
            ->where(User::DELETED_AT, '=', null)
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
        return DB::table($this->getTable())
            ->where(User::ID, '=', $userId)
            ->where(User::CREATED_AT, '=', $createdAt)
            ->where(User::DELETED_AT, '=', null)
            ->update($resource);
    }
}
