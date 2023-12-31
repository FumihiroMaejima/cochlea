<?php

declare(strict_types=1);

namespace App\Repositories\Users\Users;

use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Library\Array\ArrayLibrary;
use App\Models\User;
use App\Repositories\Users\BaseUserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class UsersRepository implements UsersRepositoryInterface
{
    protected User $model;

    protected const NO_DATA_COUNT = 0;
    protected const FIRST_DATA_COUNT = 1;

    /**
     * create instance.
     *
     * @param User $model
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
     * get by email.
     *
     * @param string $email email
     * @param bool $isLock exec lock For Update
     * @return array|null
     * @throws MyApplicationHttpException
     */
    public function getByEmail(string $email, bool $isLock = false): array|null
    {
        $query = DB::table($this->getTable())
            ->select(['*'])
            ->where(User::EMAIL, '=', $email)
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

        // return $collection;
        return ArrayLibrary::getFirst(ArrayLibrary::toArray($collection->toArray()));
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
     * @param array $resource create data
     * @return int
     */
    public function create(array $resource): int
    {
        // return DB::table($this->getTable())->insert($resource);
        return DB::table($this->getTable())->insertGetId($resource);
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
