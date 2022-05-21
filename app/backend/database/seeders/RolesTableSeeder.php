<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class RolesTableSeeder extends Seeder
{
    private const TABLE_NAME = 'roles';
    private const SEEDER_DATA_LENGTH = 5;
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
            'code'       => '',
            'detail'     => '',
            'created_at' => '2021-01-14 00:00:00',
            'updated_at' => '2021-01-14 00:00:00'
        ];

        $nameList = Config::get('myapp.seeder.authority.rolesNameList');
        $codeList = Config::get('myapp.seeder.authority.rolesCodeList');
        $detailList = Config::get('myapp.seeder.authority.rolesDetailList');

        // insert用データ
        $data = [];

        // 1~$this->countの数字の配列でforを回す
        foreach (range(1, $this->count) as $i) {
            $row = $template;

            $row['name']   = $nameList[$i - 1];
            $row['code']   = $codeList[$i - 1];
            $row['detail'] = $detailList[$i - 1];

            $data[] = $row;
        }

        // テーブルへの格納
        DB::table(self::TABLE_NAME)->insert($data);
    }
}
