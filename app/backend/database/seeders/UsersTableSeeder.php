<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Library\Time\TimeLibrary;
use App\Models\User;
use Database\Seeders\BaseSeeder;

class UsersTableSeeder extends BaseSeeder
{
    protected const SEEDER_DATA_LENGTH = 5;
    protected const SEEDER_DATA_TESTING_LENGTH = 5;
    protected const SEEDER_DATA_DEVELOP_LENGTH = 50;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->tableName = (new User())->getTable();

        // $now = Carbon::now()->timezone(Config::get('app.timezone'));
        $now = TimeLibrary::getCurrentDateTime();

        $template = [
            User::NAME       => '',
            User::EMAIL      => '',
            User::PASSWORD   => bcrypt(Config::get('myappSeeder.seeder.password.testuser')),
            User::ROLE       => 10,
            User::CREATED_AT => $now,
            User::UPDATED_AT => $now
        ];

        // insert用データ
        $data = [];

        // データ数
        $this->count = $this->getSeederDataLengthByEnv();

        // 1~$this->countの数字の配列でforを回す
        foreach (range(1, $this->count) as $i) {
            $row = $template;

            $row[User::NAME]  = 'user' . (string)($i);
            $row[User::EMAIL] = 'testuser' . (string)($i) . '@example.com';

            $data[] = $row;
        }

        // テーブルへの格納
        DB::table($this->tableName)->insert($data);
    }
}
