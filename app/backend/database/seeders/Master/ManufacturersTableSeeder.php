<?php

namespace Database\Seeders\Master;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Models\Master\Manufacturers;
use Database\Seeders\BaseSeeder;

class ManufacturersTableSeeder extends BaseSeeder
{
    protected const SEEDER_DATA_LENGTH = 5;
    protected const SEEDER_DATA_TESTING_LENGTH = 5;
    protected const SEEDER_DATA_DEVELOP_LENGTH = 50;
    protected int $count = 5;
    protected string $tableName = '';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->tableName = (new Manufacturers())->getTable();

        $now = Carbon::now()->timezone(Config::get('app.timeZone'));

        $template = [
            Manufacturers::NAME       => '',
            Manufacturers::DETAIL     => '',
            Manufacturers::ADDRESS    => 'test県test市test町',
            Manufacturers::TEL        => '000-0000-0000',
            Manufacturers::CREATED_AT => $now,
            Manufacturers::UPDATED_AT => $now
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

            $row[Manufacturers::NAME]    = 'manufacturer' . (string)($i);
            $row[Manufacturers::DETAIL]  = 'testManufacturer' . (string)($i) . 'Detail';
            $row[Manufacturers::ADDRESS] = 'test県test市test' . (string)($i) . '町';
            $row[Manufacturers::TEL]     = '000-0000-000' . (string)($i);

            $data[] = $row;
        }

        // テーブルへの格納
        DB::table($this->tableName)->insert($data);
    }
}
