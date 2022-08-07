<?php

namespace App\Library\Database;

use Illuminate\Support\Facades\Config;

class ShardingLibrary
{
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

        if ($baseConnectionName === Config::get('myapp.ci.database.baseConnectionName')) {
            return $baseConnectionName;
        }

        return $baseConnectionName . (string)$nodeNumber;
    }
}
