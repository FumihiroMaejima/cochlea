<?php

declare(strict_types=1);

namespace Database\Seeders\Masters;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Library\Time\TimeLibrary;
use App\Models\Masters\AdminsRoles;
use Database\Seeders\BaseSeeder;

class AdminsRolesTableSeeder extends BaseSeeder
{
    protected const SEEDER_DATA_LENGTH = 5;
    protected const SEEDER_DATA_TESTING_LENGTH = 5;
    protected const SEEDER_DATA_DEVELOP_LENGTH = 50;

    // 一番権限の小さいロールのID
    private const MINIMUM_ROLE_ID = 5;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->tableName = (new AdminsRoles())->getTable();

        $now = TimeLibrary::getCurrentDateTime();

        $template = [
            'admin_id'   => 1,
            'role_id'    => 1,
            'created_at' => $now,
            'updated_at' => $now
        ];

        // insert用データ
        $data = [];

        // データ数
        $this->count = $this->getSeederDataLengthByEnv();

        // 1~$this->countの数字の配列でforを回す
        foreach (range(1, $this->count) as $i) {
            $row = $template;

            $row['admin_id'] = $i;
            $row['role_id']  = $i > self::MINIMUM_ROLE_ID ? self::MINIMUM_ROLE_ID : $i;

            $data[] = $row;
        }

        // テーブルへの格納
        DB::table($this->tableName)->insert($data);
    }
}
