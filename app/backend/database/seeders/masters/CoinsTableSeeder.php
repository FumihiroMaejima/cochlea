<?php

namespace Database\Seeders\Masters;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Models\Masters\Coins;
use Database\Seeders\BaseSeeder;

class CoinsTableSeeder extends BaseSeeder
{
    protected const SEEDER_DATA_LENGTH = 5;
    protected const SEEDER_DATA_TESTING_LENGTH = 5;
    protected const SEEDER_DATA_DEVELOP_LENGTH = 10;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->tableName = (new Coins())->getTable();

        $now = Carbon::now()->timezone(Config::get('app.timezone'));
        $endDate = (new Carbon($now))->addYears(5);

        $template = [
            Coins::NAME              => '',
            Coins::DETAIL            => '',
            Coins::PRICE             => 100,
            Coins::COST              => 100,
            Coins::START_AT          => $now,
            Coins::END_AT            => $endDate,
            Coins::IMAGE             => '',
            Coins::CREATED_AT        => $now,
            Coins::UPDATED_AT        => $now
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

            // 価格
            $price = $row[Coins::PRICE] * $i;

            $row[Coins::NAME]          = 'coins_' . (string)($i) . (string)$price;
            $row[Coins::DETAIL]        = 'testCoin' . (string)($i) . '';
            $row[Coins::PRICE]         =  $price;
            $row[Coins::COST]          =  $price;
            $row[Coins::IMAGE]         = '/product/image/' . (string)($i);

            $data[] = $row;
        }

        // テーブルへの格納
        DB::table($this->tableName)->insert($data);
    }
}
