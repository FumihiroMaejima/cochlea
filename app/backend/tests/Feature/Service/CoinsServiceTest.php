<?php

namespace Tests\Feature\Service;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Tests\ServiceBaseTestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use App\Library\Message\StatusCodeMessages;
use App\Exports\Masters\Coins\CoinsTemplateExport;
use App\Http\Requests\Admin\Roles\RoleBaseRequest;
use App\Http\Requests\Admin\Coins\CoinBaseRequest;
use Database\Seeders\Masters\AdminsTableSeeder;
use Database\Seeders\Masters\AdminsRolesTableSeeder;
use Database\Seeders\Masters\CoinsTableSeeder;
use Database\Seeders\Masters\PermissionsTableSeeder;
use Database\Seeders\Masters\RolePermissionsTableSeeder;
use Database\Seeders\Masters\RolesTableSeeder;

// use Illuminate\Foundation\Testing\DatabaseMigrations;
// use Illuminate\Foundation\Testing\RefreshDatabase;

class CoinsServiceTest extends ServiceBaseTestCase
{
    // target seeders.
    protected array $seederClasses = [
        AdminsTableSeeder::class,
        CoinsTableSeeder::class,
        PermissionsTableSeeder::class,
        RolesTableSeeder::class,
        RolePermissionsTableSeeder::class,
        AdminsRolesTableSeeder::class,
    ];

    /**
     * setUpは各テストメソッドが実行される前に実行する
     * 親クラスのsetUpを必ず実行する
     */
    protected function setUp(): void
    {
        parent::setUp();
        $loginUser = [];

        if (!$this->initialized) {
            $loginUser         = $this->init();
            $this->initialized = true;
        }

        $this->withHeaders([
            Config::get('myapp.headers.id')        => $loginUser[self::INIT_REQUEST_RESPONSE_USER_ID],
            Config::get('myapp.headers.authority') => $loginUser[self::INIT_REQUEST_RESPONSE_USER_AUTHORITY],
            Config::get('myapp.headers.authorization') => self::TOKEN_PREFIX . $loginUser[self::INIT_REQUEST_RESPONSE_TOKEN],
        ]);
    }

    /**
     * coins get request test.
     *
     * @return void
     */
    public function testGetCoins(): void
    {
        $response = $this->get(route('admin.coins.index'));
        $response->assertStatus(StatusCodeMessages::STATUS_200)
            ->assertJsonCount(5, RoleBaseRequest::RESPONSE_KEY_DATA);
    }

    /**
     * coin crerate data
     * @return array
     */
    public function coinCreateDataProvider(): array
    {
        $this->createApplication();

        return [
            'create coin data' => Config::get('myappTest.test.coins.create.success')
        ];
    }

    /**
     * coin create request test.
     * @dataProvider coinCreateDataProvider
     * @return void
     */
    public function testCreateCoinSuccess(
        string $name,
        string $detail,
        int $price,
        int $cost,
        string $startAt,
        string $endAt,
        string|null $image
    ): void
    {
        $response = $this->json('POST', route('admin.coins.create'), [
            CoinBaseRequest::KEY_NAME     => $name,
            CoinBaseRequest::KEY_DETAIL   => $detail,
            CoinBaseRequest::KEY_PRICE    => $price,
            CoinBaseRequest::KEY_COST     => $cost,
            CoinBaseRequest::KEY_START_AT => $startAt,
            CoinBaseRequest::KEY_END_AT   => $endAt,
            // CoinBaseRequest::KEY_IMAGE    => $image,
        ]);
        $response->assertStatus(StatusCodeMessages::STATUS_201);
    }

    /**
     * coin crerate 422 error data
     * @return array
     */
    public function coinCreate422FailedDataProvider(): array
    {
        $this->createApplication();

        $caseKeys = [
            'no_name',
            'no_detail',
            'no_price',
            'no_cost',
            'no_start_at',
            'no_end_at',
            'price_less_than_0',
            'cost_less_than_0',
            'start_at_grater_than_end_at',
            'start_at_invalid_format'
        ];
        $testCase = [];
        foreach ($caseKeys as $key) {
            $testCase[$key] = Config::get('myappTest.test.coins.create.success');
        }

        // データの整形
        $testCase['no_name'][CoinBaseRequest::KEY_NAME]               = '';
        $testCase['no_detail'][CoinBaseRequest::KEY_DETAIL]           = '';
        $testCase['no_price'][CoinBaseRequest::KEY_PRICE]             = null;
        $testCase['no_cost'][CoinBaseRequest::KEY_COST]               = null;
        $testCase['no_start_at'][CoinBaseRequest::KEY_START_AT]       = '';
        $testCase['no_end_at'][CoinBaseRequest::KEY_END_AT]           = '';
        // $testCase['no_image'][CoinBaseRequest::KEY_IMAGE]             = '';
        $testCase['price_less_than_0'][CoinBaseRequest::KEY_PRICE] = -1;
        $testCase['cost_less_than_0'][CoinBaseRequest::KEY_PRICE]  = -1;
        $testCase['start_at_grater_than_end_at'][CoinBaseRequest::KEY_START_AT] = '2031/08/20 00:00:00';
        $testCase['start_at_invalid_format'][CoinBaseRequest::KEY_START_AT] = '2022-05-20 00:00:00';

        return $testCase;
    }

    /**
     * coin create 422 error request test.
     * @dataProvider coinCreate422FailedDataProvider
     * @return void
     */
    public function testCreateCoin422Failed(
        string $name,
        string $detail,
        int|null $price,
        int|null $cost,
        string $startAt,
        string $endAt,
        string|null $image
    ): void
    {
        $response = $this->json('POST', route('admin.coins.create'), [
            CoinBaseRequest::KEY_NAME     => $name,
            CoinBaseRequest::KEY_DETAIL   => $detail,
            CoinBaseRequest::KEY_PRICE    => $price,
            CoinBaseRequest::KEY_COST     => $cost,
            CoinBaseRequest::KEY_START_AT => $startAt,
            CoinBaseRequest::KEY_END_AT   => $endAt,
            // CoinBaseRequest::KEY_IMAGE    => $image,
        ]);
        $response->assertStatus(StatusCodeMessages::STATUS_422);
    }

    /**
     * coins file download test.
     * output dir storage/framework/laravel-excel
     *
     * @return void
     */
    public function testDownloadCoinsCsvFile(): void
    {
        $response = $this->get(route('admin.coins.download.csv'));
        $response->assertStatus(200)
            ->assertHeader('content-type', self::CONTENT_TYPE_TEXT_CSV);
    }

    /**
     * coins template file download test.
     * output dir storage/framework/laravel-excel
     *
     * @return void
     */
    public function testDownloadCoinsTemplateFile(): void
    {
        $response = $this->get(route('admin.coins.download.template'));
        $response->assertStatus(200)
            ->assertHeader('content-type', self::CONTENT_TYPE_APPLICATION_EXCEL);
    }

    /**
     * import coins filee request test.
     *
     * @return void
     */
    public function testImportCoins(): void
    {
        $name = Config::get('myappTest.test.coins.import.success')['fileName'];

        /* make file */
        // Symfony file package extends SplFileInfo
        $symfonyFile = Excel::download(
            new CoinsTemplateExport(collect(Config::get('myappTest.test.coins.import.fileData'))), $name
        )->getFile();
        $file = UploadedFile::fake()->createWithContent($name, $symfonyFile->getContent());

        $response = $this->json('POST', route('admin.coins.upload.template'), [
            'file' => $file
        ]);
        $response->assertStatus(201);
    }


    /**
     * coin update request test.
     *
     * @return void
     */
    public function testUpdateCoin(): void
    {
        $response = $this->json('PATCH', route('admin.coins.update', [CoinBaseRequest::KEY_ID => 4]), [
            CoinBaseRequest::KEY_NAME     => 'test coin name4',
            CoinBaseRequest::KEY_DETAIL   => 'test coin detail',
            CoinBaseRequest::KEY_PRICE    => 500,
            CoinBaseRequest::KEY_COST     => 500,
            CoinBaseRequest::KEY_START_AT => '2022/08/20 00:00:00',
            CoinBaseRequest::KEY_END_AT   => '2022/08/21 23:59:59',
            // CoinBaseRequest::KEY_IMAGE    => null,
        ]);
        $response->assertStatus(StatusCodeMessages::STATUS_200);
    }

    /**
     * coins update request failed test.
     *
     * @return void
     */
    public function testUpdateFailedCoin(): void
    {
        $response = $this->json('PATCH', route('admin.coins.update', [CoinBaseRequest::KEY_ID => 4]), [
            CoinBaseRequest::KEY_NAME     => 'test coin name4',
            CoinBaseRequest::KEY_DETAIL   => 'test coin detail',
            CoinBaseRequest::KEY_PRICE    => -1,
            CoinBaseRequest::KEY_COST     => 500,
            CoinBaseRequest::KEY_START_AT => '2022/08/20 00:00:00',
            CoinBaseRequest::KEY_END_AT   => '2022/08/21 23:59:59',
            CoinBaseRequest::KEY_IMAGE    => null,
        ]);
        $response->assertStatus(StatusCodeMessages::STATUS_422);
    }

    /**
     * coin delete request test.
     * @return void
     */
    public function testRemoveCoinSuccess(): void
    {
        $response = $this->json('DELETE', route('admin.coins.delete'), [
            CoinBaseRequest::KEY_COINS => [1]
        ]);
        $response->assertStatus(StatusCodeMessages::STATUS_200);
    }

    /**
     * coin delete data
     * @return array
     */
    public function coinRemoveValidationErrorDataProvider(): array
    {
        $this->createApplication();

        return [
            'no exist coins'             => [CoinBaseRequest::KEY_COINS => [100]],
            'not integer value in array' => [CoinBaseRequest::KEY_COINS => ['string']]
        ];
    }

    /**
     * coin remove validation error test.
     * @dataProvider coinRemoveValidationErrorDataProvider
     * @return void
     */
    public function testRemoveCoinValidationError(array $coins): void
    {
        $response = $this->json('DELETE', route('admin.coins.delete'), [
            CoinBaseRequest::KEY_COINS => $coins
        ]);
        $response->assertStatus(StatusCodeMessages::STATUS_422);
    }
}
