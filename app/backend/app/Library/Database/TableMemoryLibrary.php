<?php

namespace App\Library\Database;

use Illuminate\Support\Facades\DB;

class TableMemoryLibrary
{
    // keys
    public const KEY_TABLE_SCHEMA = 'TABLE_SCHEMA';
    public const KEY_TABLE_NAME = 'TABLE_NAME';
    public const KEY_PARTITION_NAME = 'PARTITION_NAME';
    public const KEY_PARTITION_ORDINAL_POSITION = 'PARTITION_ORDINAL_POSITION';
    public const KEY_TABLE_ROWS = 'TABLE_ROWS';
    public const KEY_CREATE_TIME = 'CREATE_TIME';
    public const KEY_PARTITION_DESCRIPTION = 'PARTITION_DESCRIPTION';

    // 単位ごとのバイト数
    public const BASE_UNIT_VALUE = 1024;

    /**
     * get database memories.
     *
     * @param string $connection connection name
     * @param string $sort sort setting 'ASC' or 'DESC'
     * @return array
     */
    public static function getDatabaseMemories(
        string $connection = 'mysql',
        string $sort = 'DESC'
    ): array {
        $baseValue = self::BASE_UNIT_VALUE;

        $collection = DB::connection($connection)
            ->table('INFORMATION_SCHEMA.TABLES')
            ->select(DB::raw("
                TABLE_SCHEMA,
                FLOOR(SUM(DATA_LENGTH  + INDEX_LENGTH) / $baseValue / $baseValue) AS ALL_MB,
                FLOOR(SUM((DATA_LENGTH) / $baseValue / $baseValue)) AS DATA_MB,
                FLOOR(SUM((INDEX_LENGTH) / $baseValue / $baseValue)) AS INDEX_MB
            "))
            ->groupBy('TABLE_SCHEMA')
            ->orderByRaw("SUM(DATA_LENGTH + INDEX_LENGTH) $sort")
            ->get()
            ->toArray();

        if (empty($collection)) {
            return [];
        }

        return json_decode(json_encode($collection), true);
    }
}
