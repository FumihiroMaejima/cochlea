<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;

class BaseUserDataModel extends Model
{
    /** @var string BASE_CONNECTION_NAME DBへのコネクション名のベース(prefix) */
    private const BASE_CONNECTION_NAME = 'mysql_user';

    /** @var int NODE_NUMBER_1 nodeの番号 */
    private const NODE_NUMBER_1 = 1;

    /** @var int NODE_NUMBER_2 nodeの番号 */
    private const NODE_NUMBER_2 = 2;

    /** @var int NODE_NUMBER_3 nodeの番号 */
    private const NODE_NUMBER_3 = 3;

    /** @var int SHAERD_COUNT シャードの合計数 */
    private const SHAERD_COUNT = 12;

    /** @var array<int, int> NODE1_SHARDS Node1のDBに設定されているシャードのidを格納 */
    private const NODE1_SHARDS = [1, 4, 7, 10];

    /** @var array<int, int> NODE2_SHARDS Node2のDBに設定されているシャードのidを格納 */
    private const NODE2_SHARDS = [2, 5, 8, 11];

    /** @var array<int, int> NODE3_SHARDS Node3のDBに設定されているシャードのidを格納 */
    private const NODE3_SHARDS = [3, 6, 9, 12];


    /**
     * get connection name by node number.
     *
     * @param int $userId user id.
     * @return string
     */
    public static function getConnectionByUserId(int $userId): string
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
        $connectionName = self::ggetConnectionByUserId($userId);
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
        return $userId % self::SHAERD_COUNT;
    }

    /**
     * get node name by shard id.
     *
     * @param int $shardId shard id.
     * @return int node name
     */
    public static function getNodeName(int $shardId): string
    {
        // 3で割り切れる場合はnode3
        if (($shardId % 3) === 0) {
            // user database3
            return self::BASE_CONNECTION_NAME .(string)self::NODE_NUMBER_3;
        } else if (in_array($shardId, self::NODE1_SHARDS, true)) {
            // user database1
            return self::BASE_CONNECTION_NAME .(string)self::NODE_NUMBER_1;
        } else{
            // user database2
            return self::BASE_CONNECTION_NAME .(string)self::NODE_NUMBER_2;
        }
    }
}
