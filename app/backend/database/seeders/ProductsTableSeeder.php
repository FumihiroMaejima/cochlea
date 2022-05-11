<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Models\Master\Products;

class ProductsTableSeeder extends Seeder
{
    // private const TABLE_NAME = 'game_area';
    private const SEEDER_DATA_LENGTH = 5;
    private const SEEDER_DEVELOP_DATA_LENGTH = 50;
    private int $count = 5;
    private string $tableName = '';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->tableName = (new Products())->getTable();

        $now = Carbon::now()->timezone(Config::get('app.timeZone'));
        $endDate = (new Carbon($now))->addMonth();

        $template = [
            Products::NAME              => '',
            Products::DETAIL            => '',
            Products::TYPE              => 1,
            Products::PRICE             => 600,
            Products::UNIT              => '本',
            Products::MANUFACTURE       => 'テストメーカー',
            Products::NOTICE_START_AT   => $now,
            Products::NOTICE_END_AT     => $endDate,
            Products::PURCHASE_START_AT => $now,
            Products::PURCHASE_END_AT   => $endDate,
            Products::IMAGE             => '',
            Products::CREATED_AT        => $now,
            Products::UPDATED_AT        => $now
        ];

        // insert用データ
        $data = [];

        // データ数
        $this->count = $this->getSeederDataLengthByEnv(Config::get('app.env'));

        // 1~$this->countの数字の配列でforを回す
        foreach (range(1, $this->count) as $i) {
            $row = $template;

            $row[Products::NAME]          = 'product' . (string)($i);
            $row[Products::DETAIL]        = 'testProduct' . (string)($i) . '@example.com';
            $row[Products::MANUFACTURE]  .= ' product' . (string)($i);
            $row[Products::IMAGE]         = '/product/image/' . (string)($i);

            $data[] = $row;
        }

        // テーブルへの格納
        DB::table($this->tableName)->insert($data);
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
