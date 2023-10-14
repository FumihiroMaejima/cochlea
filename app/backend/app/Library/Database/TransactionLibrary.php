<?php

namespace App\Library\Database;

use Illuminate\Support\Facades\DB;
use App\Library\Database\ShardingLibrary;

class TransactionLibrary
{
    /**
     * begin transaction in sharding table.
     *
     * @param string $connection connection name
     * @return void
     */
    public static function beginTransaction(string $connection): void
    {
        DB::connection($connection)->beginTransaction();
    }

    /**
     * commit active database transaction.
     *
     * @param string $connection connection name
     * @return void
     */
    public static function commit(string $connection): void
    {
        DB::connection($connection)->commit();
    }

    /**
     * rollback active database transaction.
     *
     * @param string $connection connection name
     * @return void
     */
    public static function rollback(string $connection): void
    {
        DB::connection($connection)->rollback();
    }

    /**
     * begin transaction in sharding table.
     *
     * @param int $userId user id
     * @return void
     */
    public static function beginTransactionByUserId(int $userId): void
    {
        DB::connection(ShardingLibrary::getConnectionByUserId($userId))->beginTransaction();
    }

    /**
     * commit active database transaction.
     *
     * @param int $userId user id
     * @return void
     */
    public static function commitByUserId(int $userId): void
    {
        DB::connection(ShardingLibrary::getConnectionByUserId($userId))->commit();
    }

    /**
     * rollback active database transaction.
     *
     * @param int $userId user id
     * @return void
     */
    public static function rollbackByUserId(int $userId): void
    {
        DB::connection(ShardingLibrary::getConnectionByUserId($userId))->rollback();
    }

    /**
     * begin transaction in sharding table by some user ids.
     *
     * @param array $userIds user ids
     * @return void
     */
    public static function beginTransactionByUserIds(array $userIds): void
    {
        foreach ($userIds as $userId) {
            DB::connection(ShardingLibrary::getConnectionByUserId($userId))->beginTransaction();
        }
    }

    /**
     * commit active database transaction by some user ids.
     *
     * @param array $userIds user ids
     * @return void
     */
    public static function commitTransactionByUserIds(array $userIds): void
    {
        foreach ($userIds as $userId) {
            DB::connection(ShardingLibrary::getConnectionByUserId($userId))->commit();
        }
    }

    /**
     * rollback active database transaction by some user ids.
     *
     * @param array $userIds user ids
     * @return void
     */
    public static function rollbackTransactionByUserIds(array $userIds): void
    {
        foreach ($userIds as $userId) {
            DB::connection(ShardingLibrary::getConnectionByUserId($userId))->rollback();
        }
    }
}
