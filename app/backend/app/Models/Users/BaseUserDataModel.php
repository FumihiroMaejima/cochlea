<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use App\Library\Database\ShardingLibrary;

class BaseUserDataModel extends Model
{
    public const USER_ID = 'userId';

    /**
     * get connection name by user id.
     *
     * @param int $userId user id.
     * @return string
     */
    public static function getConnectionNameByUserId(int $userId): string
    {
        return ShardingLibrary::getUserDataBaseConnection(self::getShardId($userId));
    }

    /**
     * get connection name by node number.
     *
     * @param int $userId user id.
     * @return string
     */
    /* public static function setConnectionName(int $userId): string
    {
        $connectionName = self::getConnectionNameByUserId($userId);
        return $connectionName;
        // return parent::setConnection($connectionName);
    } */

    /**
     * get shard id by user id.
     *
     * @param int $userId user id.
     * @return int shard id
     */
    public static function getShardId(int $userId): int
    {
        // 除算の余り
        return ShardingLibrary::getShardIdByUserId($userId);
    }

    /**
     * get Model Table Name by user id for sharding setting.
     *
     * @param int $userId user id
     * @return string
     */
    public function getTableByUserId(int $userId): string
    {
        return $this->getTable() . self::getShardId($userId);
    }

    /**
     * get query builder by user id.
     *
     * @param int $userId user id
     * @return Builder
     */
    public function getQueryBuilder(int $userId): Builder
    {
        return DB::connection(self::getConnectionNameByUserId($userId))
            ->table($this->getTableByUserId($userId));
    }

    /**
     * get all record by user id.
     *
     * @param int $userId user id
     * @return array
     */
    public function getAllbyUserId(int $userId): array
    {
        return (new UserCoins())
            ->getQueryBuilder($userId)
            ->where(static::USER_ID, '=', $userId)
            ->get()
            ->toArray();
    }
}
