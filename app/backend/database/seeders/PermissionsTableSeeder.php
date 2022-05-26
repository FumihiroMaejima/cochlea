<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Models\Permissions;
use Database\Seeders\BaseSeeder;

class PermissionsTableSeeder extends BaseSeeder
{
    protected const SEEDER_DATA_LENGTH = 4;
    protected const SEEDER_DATA_TESTING_LENGTH = 4;
    protected const SEEDER_DEVELOP_DATA_LENGTH = 4;
    protected int $count = 4;
    protected string $tableName = '';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->tableName = (new Permissions())->getTable();

        $now = Carbon::now()->timezone(Config::get('app.timeZone'));

        $template = [
            'name'       => '',
            'created_at' => $now,
            'updated_at' => $now
        ];

        $dataList = Config::get('myapp.seeder.authority.permissionsNameList');

        // データ数
        $this->count = $this->_getSeederDataLengthByEnv(Config::get('app.env'));

        // insert用データ
        $data = [];

        // 1~$this->countの数字の配列でforを回す
        foreach (range(1, $this->count) as $i) {
            $row = $template;

            $row['name'] = $dataList[$i - 1];

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
