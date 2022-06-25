<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class BaseUserDataModel extends Model
{
    /** @var array<int, int> NODE1_SHARDS Node1のDBに設定されているシャードのidを格納 */
    private const NODE1_SHARDS = [1, 4, 7, 10];

    /** @var array<int, int> NODE2_SHARDS Node2のDBに設定されているシャードのidを格納 */
    private const NODE2_SHARDS = [2, 5, 8, 11];

    /** @var array<int, int> NODE3_SHARDS Node3のDBに設定されているシャードのidを格納 */
    private const NODE3_SHARDS = [3, 6, 9, 12];

    /**
     * get connection name by user id.
     *
     * @param int $userId user id.
     * @return string
     */
    public static function getConnectionNameByUserId(int $userId): string
    {
        return self::getNodeName(self::getShardId($userId));
    }

    /**
     * get connection name by node number.
     *
     * @param int $userId user id.
     * @return string
     */
    public static function setConnectionName(int $userId): string
    {
        $connectionName = self::getConnectionNameByUserId($userId);
        return parent::setConnection($connectionName);
    }

    /**
     * get shard id by user id.
     *
     * @param int $userId user id.
     * @return int shard id
     */
    public static function getShardId(int $userId): int
    {
        // 除算の余り
        return $userId % Config::get('myapp.databese.users.shardCount');
    }

    /**
     * get node name by shard id.
     *
     * @param int $shardId shard id.
     * @return int node name
     */
    public static function getNodeName(int $shardId): string
    {
        $baseConnectionName = Config::get('myapp.databese.users.baseConnectionName');

        // 3で割り切れる場合はnode3
        if (($shardId % Config::get('myapp.databese.users.modBaseNumber')) === 0) {
            // user database3
            return $baseConnectionName .(string)Config::get('myapp.databese.users.nodeNumber3');
        } else if (in_array($shardId, Config::get('myapp.databese.users.node1ShardIds'), true)) {
            // user database1
            return $baseConnectionName .(string)Config::get('myapp.databese.users.nodeNumber1');
        } else{
            // user database2
            return $baseConnectionName .(string)Config::get('myapp.databese.users.nodeNumber2');
        }
    }
}
