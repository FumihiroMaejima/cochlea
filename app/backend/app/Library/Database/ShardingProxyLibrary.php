<?php

namespace App\Library\Database;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Library\Array\ArrayLibrary;
use App\Library\Database\ShardingLibrary;

class ShardingProxyLibrary
{
    /**
     * get connection name by shard key.
     *
     * @param int $shardKey shard key.
     * @return string
     */
    public static function getConnectionNameByShardKey(int $shardKey): string
    {
        return ShardingLibrary::getUserDataBaseConnection(ShardingLibrary::getShardIdByNumber($shardKey));
    }

    /**
     * group connectio by shard ids.
     *
     * @param array $shardKeys user ids
     * @return array
     */
    public static function groupSharadIdsByConnection(array $shardKeys): array
    {
        return array_reduce($shardKeys, function (array $groups, int $shardKey) {
            $groups[self::getConnectionNameByShardKey($shardKey)][] = $shardKey;
            return $groups;
        }, []);
    }

    /**
     * get connectio and shard id group by shard keys.
     *
     * @param array $shardKeys shard keys
     * @return array
     */
    public static function getConnectionAndShardIdGroupByShardIds(array $shardKeys): array
    {
        $result = [];
        $shardKeysGroupByConnection = self::groupSharadIdsByConnection($shardKeys);
        foreach ($shardKeysGroupByConnection as $connection => $tmpShardKeys) {
            foreach ($tmpShardKeys as $shardKey) {
                $result[$connection][ShardingLibrary::getShardIdByNumber($shardKey)][] = $shardKey;
            }
        }
        return $result;
    }

    /**
     * get database node number & shard ids setting.
     *
     * @param string $table table name
     * @param array $columns columns
     * @param ?array $wheres conditions
     * @return array
     */
    public static function select(
        string $table,
        array $columns = ['*'],
        ?array $wheres = null
    ): array {
        $records = [];

        $connections = self::getConnectionAndShardIdGroupByShardIds(range(1, 16));
        $result = [];
        foreach ($connections as $connection => $shardIds) {
            foreach ($shardIds as $shardId => $_) {
                $query = DB::connection($connection)
                    ->table($table . $shardId)
                    ->select($columns);

                if (!is_null($wheres)) {
                    foreach ($wheres as $column => $condition) {
                        $query = $query->where($column, '=', $condition);
                    }
                }

                $records = $query->get()->toArray();
                $result = array_merge($result, $records);
            }
        }

        return ArrayLibrary::toArray($result);;
    }
}
