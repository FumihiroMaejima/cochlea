<?php

namespace App\Library\Database;

use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\MySqlSchemaState;

class DumpLibrary
{
    // keys
    public const KEY_TABLE_SCHEMA = 'TABLE_SCHEMA';
    public const KEY_TABLE_NAME = 'TABLE_NAME';
    public const KEY_PARTITION_NAME = 'PARTITION_NAME';
    public const KEY_PARTITION_ORDINAL_POSITION = 'PARTITION_ORDINAL_POSITION';
    public const KEY_TABLE_ROWS = 'TABLE_ROWS';
    public const KEY_CREATE_TIME = 'CREATE_TIME';
    public const KEY_PARTITION_DESCRIPTION = 'PARTITION_DESCRIPTION';

    /**
     * dump database.
     *
     * @param string $connection database connection
     * @param string $path file path
     * @return void
     */
    public static function dump(string $connection, string $path): void
    {
        // \Illuminate\Contracts\Foundation\Application をパラメーターとして渡す必要がある
        $app = app();
        $connectionInstance = (new DatabaseManager($app,new ConnectionFactory($app)))->connection($connection);
        // sqliteを使う場合の設定は現状考慮外
        (new MySqlSchemaState($connectionInstance))->dump($connectionInstance, $path);
    }
}
