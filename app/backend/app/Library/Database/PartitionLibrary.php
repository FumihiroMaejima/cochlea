<?php

namespace App\Library\Database;

use Illuminate\Support\Facades\DB;

class PartitionLibrary
{
    /**
     * create partiions by range
     *
     * @param string $databaseName database name
     * @param string $tableName table name
     * @param string $columnName column name
     * @param string $$partitions partition setting statemetns
     * @return void
     */
    public static function createPartitionsByRange(string $databaseName, string $tableName, string $columnName, string $partitions): void
    {
        DB::statement(
            "
                ALTER TABLE ${databaseName}.${tableName}
                PARTITION BY RANGE COLUMNS(${columnName}) (
                    ${partitions}
                )
            "
        );
    }

    /**
     * create partiions by hash
     * (指定カラムをパーティション化キーとして使用して HASH によって$countつのパーティションにパーティション化)
     *
     * @param string $databaseName database name
     * @param string $tableName table name
     * @param string $columnName column name
     * @param string $divCount div count
     * @param int $count partition count
     * @return void
     */
    public static function createPartitionsByHashDiv(
        string $databaseName,
        string $tableName,
        string $columnName,
        string $divCount,
        int $count
    ): void {
        DB::statement(
            "
                ALTER TABLE ${databaseName}.${tableName}
                PARTITION BY HASH(${columnName} div ${divCount})
                PARTITIONS ${count};
            "
        );
    }

    /**
     * add partiions
     *
     * @param string $databaseName database name
     * @param string $tableName table name
     * @param string $$partitions partition setting statemetns
     * @return void
     */
    public static function addPartitions(string $databaseName, string $tableName, string $partitions): void
    {
        DB::statement(
            "
                ALTER TABLE ${databaseName}.${tableName}
                ADD PARTITION (
                    ${partitions}
                )
            "
        );
    }

    /**
     * delete partiion.
     *
     * @param string $databaseName database name
     * @param string $tableName table name
     * @param string $partitionName partition name
     * @return void
     */
    public static function deletePartition(string $databaseName, string $tableName, string $partitionName): void
    {
        DB::statement(
            "
                ALTER TABLE ${databaseName}.${tableName} DROP PARTITION ${partitionName};
            "
        );
    }
}
