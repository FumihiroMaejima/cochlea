<?php

namespace App\Library\Database;

use Illuminate\Support\Facades\Config;

class DatabaseLibrary
{
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
