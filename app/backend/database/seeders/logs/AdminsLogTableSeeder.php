<?php

declare(strict_types=1);

namespace Database\Seeders\Logs;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Library\Time\TimeLibrary;
use App\Models\Logs\AdminsLog;
use Database\Seeders\BaseSeeder;

class AdminsLogTableSeeder extends BaseSeeder
{
    private int $count = 5;

    protected const SEEDER_DATA_LENGTH = 5;
    protected const SEEDER_DATA_TESTING_LENGTH = 5;
    protected const SEEDER_DATA_DEVELOP_LENGTH = 100;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->tableName = (new AdminsLog())->getTable();

        $now = TimeLibrary::getCurrentDateTime();

        $template = [
            'admin_id'    => 1,
            'function'    => 'GET',
            'status'      => '200',
            'action_time' => $now,
            'created_at'  => $now,
            'updated_at'  => $now
        ];

        // insert用データ
        $data = [];

        // データ数
        $this->count = $this->getSeederDataLengthByEnv();

        // 1~$this->countの数字の配列でforを回す
        foreach (range(1, $this->count) as $i) {
            $row = $template;

            $row['function']    = ($i % 2 === 0) ? 'GET' : 'POST';
            $row['status']      = 'admins log' . ($i % 2 === 0) ? '200' : '404';
            $row['action_time'] = $now;

            $data[] = $row;
        }

        // テーブルへの格納
        DB::table($this->tableName)->insert($data);
    }
}
