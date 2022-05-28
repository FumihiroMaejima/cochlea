<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Models\Master\Products;

class BaseSeeder extends Seeder
{
    // private const TABLE_NAME = 'table_name';
    protected const SEEDER_DATA_LENGTH = 10;
    protected const SEEDER_DATA_TESTING_LENGTH = 10;
    protected const SEEDER_DATA_DEVELOP_LENGTH = 10;
    protected int $count = 10;
    protected string $tableName = '';

    /**
     * get data length by env.
     *
     * @param string $envName 環境の値(local,stg,production,testingなど)
     * @param int $productionLength production時のインサートするデータ数
     * @param int $testingLength testing時のインサートするデータ数
     * @param int $developLength localや開発時のインサートするデータ数
     * @return int
     */
    protected function getSeederDataLengthByEnv(
        string $envName,
        int $productionLength = self::SEEDER_DATA_LENGTH,
        int $testingLength = self::SEEDER_DATA_TESTING_LENGTH,
        int $developLength = self::SEEDER_DATA_DEVELOP_LENGTH,
    ): int
    {
        if ($envName === 'production') {
            return $productionLength;
        } elseif ($envName === 'testing') {
            // testの時
            return $testingLength;
        } else {
            // localやstaging
            return $developLength;
            // return self::SEEDER_DATA_LENGTH;
        }
    }
}
