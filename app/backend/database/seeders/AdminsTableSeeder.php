<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Models\Admins;
use Database\Seeders\BaseSeeder;

class AdminsTableSeeder extends BaseSeeder
{
    protected const SEEDER_DATA_LENGTH = 5;
    protected const SEEDER_DATA_TESTING_LENGTH = 5;
    protected const SEEDER_DEVELOP_DATA_LENGTH = 50;
    protected int $count = 5;
    protected string $tableName = '';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->tableName = (new Admins())->getTable();

        $now = Carbon::now()->timezone(Config::get('app.timeZone'));

        $template = [
            Admins::NAME       => '',
            Admins::EMAIL      => '',
            Admins::PASSWORD   => bcrypt(Config::get('myapp.seeder.password.testadmin')),
            Admins::CREATED_AT => $now,
            Admins::UPDATED_AT => $now
        ];

        // insert用データ
        $data = [];

        // データ数
        $this->count = $this->getSeederDataLengthByEnv(Config::get('app.env'));

        // 1~$this->countの数字の配列でforを回す
        foreach (range(1, $this->count) as $i) {
            $row = $template;

            $row[Admins::NAME]  = 'admin' . (string)($i);
            $row[Admins::EMAIL] = 'testadmin' . (string)($i) . '@example.com';

            $data[] = $row;
        }

        // テーブルへの格納
        DB::table($this->tableName)->insert($data);
    }

    /**
     * get data length by env in parent class pethod.
     *
     * @param string $envName 環境の値(local,stg,production,testingなど)
     * @param int $productionLength production時のインサートするデータ数
     * @param int $testingLength testing時のインサートするデータ数
     * @param int $developLength localや開発時のインサートするデータ数
     * @return int
     */
    protected function _getSeederDataLengthByEnv(
        string $envName,
        int $productionLength = self::SEEDER_DATA_LENGTH,
        int $testingLength = self::SEEDER_DATA_TESTING_LENGTH,
        int $developLength = self::SEEDER_DEVELOP_DATA_LENGTH,
    ): int
    {
        return parent::getSeederDataLengthByEnv($envName, $productionLength, $testingLength, $developLength);
    }
}
