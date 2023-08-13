<?php

namespace Database\Seeders\Users;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Library\Database\ShardingLibrary;
use App\Library\Time\TimeLibrary;
use App\Models\Masters\Coins;
use App\Models\Users\UserCoinHistories;
use App\Models\Users\UserCoins;
use Database\Seeders\BaseSeeder;

class UserCoinsTableSeeder extends BaseSeeder
{
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
        $this->tableName = (new UserCoins())->getTable();

        $now = TimeLibrary::getCurrentDateTime();

        $template = [
            UserCoins::USER_ID            => 0,
            UserCoins::FREE_COINS         => 0,
            UserCoins::PAID_COINS         => 0,
            UserCoins::LIMITED_TIME_COINS => 0,
            UserCoins::CREATED_AT         => $now,
            UserCoins::UPDATED_AT         => $now
        ];

        // insert用データ
        $data = [];

        // データ数
        $this->count = $this->getSeederDataLengthByEnv();

        // 1~$this->countの数字の配列でforを回す
        foreach (range(1, $this->count) as $i) {
            $row = $template;

            // 価格
            $price = rand(0, 50) * $i;

            $row[UserCoins::USER_ID]            = $i;
            $row[UserCoins::FREE_COINS]         = ceil($price / 2);
            $row[UserCoins::PAID_COINS]         = $price;
            $row[UserCoins::LIMITED_TIME_COINS] = ceil($price / 10);

            $data[] = $row;
        }

        $model = (new UserCoins());
        foreach ($data as $row) {
            $model->getQueryBuilder($row[UserCoins::USER_ID])->insert($data);
        }
    }
}
