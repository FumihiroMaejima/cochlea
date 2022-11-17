<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use App\Library\Array\ArrayLibrary;
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
    public function getAllByUserId(int $userId): array
    {
        return $this->getQueryBuilder($userId)
            ->where(static::USER_ID, '=', $userId)
            ->get()
            ->toArray();
    }

    /**
     * get single Record record by user id.
     *
     * @param int $userId user id
     * @param bool $isLock exec lock For Update
     * @return array|null
     */
    public function getRecordByUserId(int $userId, bool $isLock = false): array|null
    {
        $query = $this->getQueryBuilder($userId)
            ->where(static::USER_ID, '=', $userId);

        if ($isLock) {
            $query->lockForUpdate();
        }

        $record = $query->first();

        if (empty($record)) {
            return null;
        }

        return ArrayLibrary::toArray($record);
    }

    /**
     * insert record.
     *
     * @param int $userId user id
     * @param array $$resource resource
     * @return bool
     */
    public function insert(int $userId, array $resource): bool
    {
        return $this->getQueryBuilder($userId)->insert($resource);
    }
}
