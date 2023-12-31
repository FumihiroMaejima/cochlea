<?php

declare(strict_types=1);

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use App\Library\Array\ArrayLibrary;
use App\Library\Database\ShardingLibrary;
use App\Library\Database\ShardingProxyLibrary;
use App\Library\Time\TimeLibrary;

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
        return ShardingLibrary::getShardIdByNumber($userId);
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
     * get connectio and shard id ans resources group by user ids.
     *
     * @param array $userIds user ids
     * @return array
     */
    public static function getConnectionAndShardIdAndResourcesGroupByUserIds(array $userIds, array $resources): array
    {
        $result = [];
        $userIdsGroupByConnection = ShardingProxyLibrary::groupShardKeysByConnection($userIds);
        foreach ($userIdsGroupByConnection as $connection => $tmpUserIds) {
            foreach ($tmpUserIds as $userId) {
                if (empty($resources[$userId])) {
                    continue;
                }
                $result[$connection][self::getShardId($userId)][] = $resources[$userId];
            }
        }
        return $result;
    }

    /**
     * get connectio and shard id and multi resources group by user ids.
     *
     * @param array $userIds user ids
     * @return array
     */
    public static function getConnectionAndShardIdAndMultiResourcesGroupByUserIds(array $userIds, array $resources): array
    {
        $result = [];
        $userIdsGroupByConnection = ShardingProxyLibrary::groupShardKeysByConnection($userIds);
        foreach ($userIdsGroupByConnection as $connection => $tmpUserIds) {
            foreach ($tmpUserIds as $userId) {
                if (empty($resources[$userId])) {
                    continue;
                }
                $resourcesGroupByUserId = $resources[$userId];
                $shardId = self::getShardId($userId);
                foreach ($resourcesGroupByUserId as $tmpResource) {
                    $result[$connection][$shardId][] = $tmpResource;
                }
            }
        }
        return $result;
    }

    /**
     * get all records by user id.
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
        $connections = ShardingProxyLibrary::getConnectionAndShardIdGroupByShardKeys($userIds);
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
     * insert records.
     *
     * @param array $userIds user ids
     * @param array $resource resource
     * @return bool
     */
    public function insertByUserIds(array $userIds, array $resources): bool
    {
        $connections = self::getConnectionAndShardIdAndResourcesGroupByUserIds($userIds, $resources);
        $result = [];
        foreach ($connections as $connection => $shardIds) {
            foreach ($shardIds as $shardId => $tmpResources) {
                $result[] = DB::connection($connection)
                    ->table($this->getTable() . $shardId)
                    ->insert($tmpResources);
            }
        }

        return true;
    }

    /**
     * insert multi user records.
     *
     * @param array $userIds user ids
     * @param array $resource resource
     * @return bool
     */
    public function insertByUserIdsForMultiUserRecords(array $userIds, array $resources): bool
    {
        $connections = self::getConnectionAndShardIdAndMultiResourcesGroupByUserIds($userIds, $resources);
        $result = [];
        foreach ($connections as $connection => $shardIds) {
            foreach ($shardIds as $shardId => $tmpResources) {
                $result[] = DB::connection($connection)
                    ->table($this->getTable() . $shardId)
                    ->insert($tmpResources);
            }
        }

        return true;
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

    /**
     * sort records by updated at.
     *
     * @param int $userId user id
     * @param array $resource resource
     * @return array
     */
    public static function sortByUpdatedAt(array $records, int $order = SORT_ASC): array
    {
        $timeStamps = [];
        foreach ($records as $record) {
            $timeStamps[] = TimeLibrary::strToTimeStamp($record[self::UPDATED_AT]);
        }

        // ソート
        array_multisort($timeStamps, $order, $records);
        return $records;
    }
}
