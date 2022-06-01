<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Models\AdminsRoles;
use Database\Seeders\BaseSeeder;

class AdminsRolesTableSeeder extends BaseSeeder
{
    protected const SEEDER_DATA_LENGTH = 5;
    protected const SEEDER_DATA_TESTING_LENGTH = 5;
    protected const SEEDER_DATA_DEVELOP_LENGTH = 5;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->tableName = (new AdminsRoles())->getTable();

        $now = Carbon::now()->timezone(Config::get('app.timeZone'));

        $template = [
            'admin_id'   => 1,
            'role_id'    => 1,
            'created_at' => $now,
            'updated_at' => $now
        ];

        // insert用データ
        $data = [];

        // データ数
        $this->count = $this->getSeederDataLengthByEnv(
            Config::get('app.env'),
            self::SEEDER_DATA_LENGTH,
            self::SEEDER_DATA_TESTING_LENGTH,
            self::SEEDER_DATA_DEVELOP_LENGTH
        );

        // 1~$this->countの数字の配列でforを回す
        foreach (range(1, $this->count) as $i) {
            $row = $template;

            $row['admin_id'] = $i;
            $row['role_id']  = $i;

            $data[] = $row;
        }

        // テーブルへの格納
        DB::table($this->tableName)->insert($data);
    }
}
