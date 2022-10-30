<?php

namespace App\Library\Database;

use Illuminate\Support\Facades\Config;

class ShardingLibrary
{
    /** @var string CONNECTION_NAME_FOR_CI CIなどで使う場合のコネクション名。単一のコネクションに接続させる。 */
    private const CONNECTION_NAME_FOR_CI = 'sqlite';
    /** @var string CONNECTION_NAME_FOR_TESTING UnitTestで使う場合のコネクション名。単一のコネクションに接続させる。 */
    private const CONNECTION_NAME_FOR_TESTING = 'mysql_testing';

    /**
     * get database node number & shard ids setting.
     *
     * @param int $nodeNumber node number
     * @return array<int, array<int, int>> ユーザー用DBのノード数(番号)とシャードid
     */
    public static function getShardingSetting(): array
    {
        return [
            Config::get('myapp.database.users.nodeNumber1') => Config::get('myapp.database.users.node1ShardIds'),
            Config::get('myapp.database.users.nodeNumber2') => Config::get('myapp.database.users.node2ShardIds'),
            Config::get('myapp.database.users.nodeNumber3') => Config::get('myapp.database.users.node3ShardIds'),
        ];
    }

    /**
     * get connection name by node number.
     *
     * @param int $nodeNumber node number
     * @return string
     */
    public static function getConnectionByNodeNumber(int $nodeNumber): string
    {
        $baseConnectionName = Config::get('myapp.database.users.baseConnectionName');

        if (in_array($baseConnectionName, self::getSingleDatabaseConnections(), true)) {
            return $baseConnectionName;
        }

        return $baseConnectionName . (string)$nodeNumber;
    }

    /**
     * get shard id by user id.
     *
     * @param int $userId user id.
     * @return int shard id
     */
    public static function getShardIdByUserId(int $userId): int
    {
        // 除算の余り
        return $userId % Config::get('myapp.database.users.shardCount');
    }

    /**
     * get user database connection name by shard id.
     *
     * @param int $shardId shard id.
     * @return int node name
     */
    public static function getUserDataBaseConnection(int $shardId): string
    {
        $baseConnectionName = Config::get('myapp.database.users.baseConnectionName');

        if (in_array($baseConnectionName, self::getSingleDatabaseConnections(), true)) {
            return $baseConnectionName;
        }

        // 3で割り切れる場合はnode3
        if (($shardId % Config::get('myapp.database.users.modBaseNumber')) === 0) {
            // user database3
            return $baseConnectionName .(string)Config::get('myapp.database.users.nodeNumber3');
        } elseif (in_array($shardId, Config::get('myapp.database.users.node1ShardIds'), true)) {
            // user database1
            return $baseConnectionName .(string)Config::get('myapp.database.users.nodeNumber1');
        } else {
            // user database2
            return $baseConnectionName .(string)Config::get('myapp.database.users.nodeNumber2');
        }
    }

    /**
     * get database connections for using single database.
     *
     * @return array<int, string> 単一DBで運用する用のDBコネクション名の配列
     */
    public static function getSingleDatabaseConnections(): array
    {
        return [
            Config::get('myapp.unitTest.database.baseConnectionName'),
            Config::get('myapp.ci.database.baseConnectionName'),
        ];
    }

    /**
     * get single database connection name from config.
     *
     * @return string
     */
    public static function getSingleConnectionByConfig(): string
    {
        $logsConnectionName = Config::get('myapp.database.logs.baseConnectionName');
        $userConnectionName = Config::get('myapp.database.users.baseConnectionName');

        // CI用ののコネクション
        if (($logsConnectionName === self::CONNECTION_NAME_FOR_CI) && ($userConnectionName === self::CONNECTION_NAME_FOR_CI)) {
            return self::CONNECTION_NAME_FOR_CI;
        } else {
            // テスト用DB内のテーブルのコネクション
            return self::CONNECTION_NAME_FOR_TESTING;
        }
    }

    /**
     * get single database connection name from config.
     *
     * @return string 単一DBで運用する用のDBコネクション名の配列
     */
    public static function getDatabaseNameByConnection($connection): string
    {
        return Config::get("database.connections.$connection.database");
    }
}
