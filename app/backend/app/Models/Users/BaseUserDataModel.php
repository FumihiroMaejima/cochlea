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
    public const DELETED_AT = 'deleted_at';

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
     * group connectio by user ids.
     *
     * @param array $userIds user ids
     * @return array
     */
    public static function groupUserIdsByConnection(array $userIds): array
    {
        return array_reduce($userIds, function (array $groups, int $userId) {
            $groups[self::getConnectionNameByUserId($userId)][] = $userId;
            return $groups;
        }, []);
    }

    /**
     * get connectio and shard id group by user ids.
     *
     * @param array $userIds user ids
     * @return array
     */
    public static function getConnectionAndShardIdGroupByUserIds(array $userIds): array
    {
        $result = [];
        $userIdsGroupByConnection = self::groupUserIdsByConnection($userIds);
        foreach ($userIdsGroupByConnection as $connection => $tmpUserIds){
            foreach($tmpUserIds as $userId) {
                $result[$connection][self::getShardId($userId)][] = $userId;
            }
        }
        return $result;
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
     * get all records by user ids.
     *
     * @param array $userIds user ids
     * @return array<int, array>
     */
    public function getAllByUserIds(array $userIds): array
    {
        $connections = self::getConnectionAndShardIdGroupByUserIds($userIds);
        $result = [];
        foreach ($connections as $connection => $shardIds) {
            foreach ($shardIds as $shardId => $tmpUserIds) {
                $records = DB::connection($connection)
                ->table($this->getTable() . $shardId)
                ->whereIn(static::USER_ID, $tmpUserIds)
                ->get()
                ->toArray();

                $result = array_merge($result, $records);
            }
        }

        return ArrayLibrary::toArray($result);
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
     * @param array $resource resource
     * @return bool
     */
    public function insertByUserId(int $userId, array $resource): bool
    {
        return $this->getQueryBuilder($userId)->insert($resource);
    }

    /**
     * insert record.
     *
     * @param int $userId user id
     * @param array $resource resource
     * @return int
     */
    public function updateByUserId(int $userId, array $resource): int
    {
        return $this->getQueryBuilder($userId)
            ->where(static::USER_ID, '=', $userId)
            ->where(static::DELETED_AT, '=', null)
            ->update($resource)
            ;
    }
}
