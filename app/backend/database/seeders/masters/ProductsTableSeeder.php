<?php

namespace Database\Seeders\Masters;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Library\TimeLibrary;
use App\Models\Masters\Products;
use Database\Seeders\BaseSeeder;

class ProductsTableSeeder extends BaseSeeder
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
        $this->tableName = (new Products())->getTable();

        $now = TimeLibrary::getCurrentDateTime();
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
        $this->count = $this->getSeederDataLengthByEnv(
            Config::get('app.env'),
            self::SEEDER_DATA_LENGTH,
            self::SEEDER_DATA_TESTING_LENGTH,
            self::SEEDER_DATA_DEVELOP_LENGTH
        );

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
}
