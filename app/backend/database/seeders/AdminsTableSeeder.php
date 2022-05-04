<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class AdminsTableSeeder extends Seeder
{
    private const TABLE_NAME = 'admins';
    private const SEEDER_DATA_LENGTH = 5;
    private const SEEDER_DEVELOP_DATA_LENGTH = 50;
    private int $count = 5;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $template = [
            'name'       => '',
            'email'      => '',
            'password'   => bcrypt(Config::get('myapp.seeder.password.testadmin')),
            'created_at' => '2022-01-04 00:00:00',
            'updated_at' => '2022-01-04 00:00:00'
        ];

        // insert用データ
        $data = [];

        // データ数
        $this->count = $this->getSeederDataLengthByEnv(Config::get('app.env'));

        // 1~$this->countの数字の配列でforを回す
        foreach (range(1, $this->count) as $i) {
            $row = $template;

            $row['name']  = 'admin' . (string)($i);
            $row['email'] = 'testadmin' . (string)($i) . '@example.com';

            $data[] = $row;
        }

        // テーブルへの格納
        DB::table(self::TABLE_NAME)->insert($data);
    }

    /**
     * get data length by env.
     * @param string $envName
     *
     * @return int
     */
    private function getSeederDataLengthByEnv(string $envName): int
    {
        if ($envName === 'production') {
            return self::SEEDER_DATA_LENGTH;
        } elseif ($envName === 'testing') {
            // testの時
            return self::SEEDER_DATA_LENGTH;
        } else {
            // localやstaging
            return self::SEEDER_DEVELOP_DATA_LENGTH;
            // return self::SEEDER_DATA_LENGTH;
        }
    }
}
