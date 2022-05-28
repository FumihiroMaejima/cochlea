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
    protected const SEEDER_DATA_DEVELOP_LENGTH = 4;

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
        $this->count = $this->getSeederDataLengthByEnv(
            Config::get('app.env'),
            self::SEEDER_DATA_LENGTH,
            self::SEEDER_DATA_TESTING_LENGTH,
            self::SEEDER_DATA_DEVELOP_LENGTH
        );

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
}
