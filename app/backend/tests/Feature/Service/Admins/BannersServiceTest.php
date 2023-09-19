<?php

namespace Tests\Feature\Service\Admins;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Tests\AdminServiceBaseTestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use App\Library\Banner\BannerLibrary;
use App\Library\Message\StatusCodeMessages;
use App\Exports\Masters\Banners\BannersBulkInsertTemplateExport;
use App\Exports\Masters\Events\EventsBulkInsertTemplateExport;
use App\Http\Requests\Admin\Banners\BannerBaseRequest;
use Database\Seeders\Masters\AdminsTableSeeder;
use Database\Seeders\Masters\AdminsRolesTableSeeder;
use Database\Seeders\Masters\BannersTableSeeder;
use Database\Seeders\Masters\PermissionsTableSeeder;
use Database\Seeders\Masters\RolePermissionsTableSeeder;
use Database\Seeders\Masters\RolesTableSeeder;

// use Illuminate\Foundation\Testing\DatabaseMigrations;
// use Illuminate\Foundation\Testing\RefreshDatabase;

class BannersServiceTest extends AdminServiceBaseTestCase
{
    // target seeders.
    protected const SEEDER_CLASSES = [
        AdminsTableSeeder::class,
        BannersTableSeeder::class,
        PermissionsTableSeeder::class,
        RolesTableSeeder::class,
        RolePermissionsTableSeeder::class,
        AdminsRolesTableSeeder::class,
    ];

    /**
     * banners get request test.
     *
     * @return void
     */
    public function testGetBanners(): void
    {
        $response = $this->get(route('admin.banners.index'), self::getHeaders());
        $response->assertStatus(StatusCodeMessages::STATUS_200)
            ->assertJsonCount(5, self::RESPONSE_KEY_DATA);
    }

    /**
     * banner crerate data
     * @return array
     */
    public static function bannerCreateDataProvider(): array
    {
        self::createApplicationForStaticDataProvider();

        return [
            'create banner data' => Config::get('myappTest.test.banners.create.success')
        ];
    }

    /**
     * banner create request test.
     * @dataProvider bannerCreateDataProvider
     * @return void
     */
    public function testCreateBannerSuccess(
        string $name,
        string $detail,
        string $location,
        int $pcHeight,
        int $pcWidth,
        int $spHeight,
        int $spWidth,
        string $startAt,
        string $endAt,
        string $url
    ): void {
        $response = $this->json('POST', route('admin.banners.create'), [
            BannerBaseRequest::KEY_NAME      => $name,
            BannerBaseRequest::KEY_DETAIL    => $detail,
            BannerBaseRequest::KEY_LOCATION  => $location,
            BannerBaseRequest::KEY_PC_HEIGHT => $pcHeight,
            BannerBaseRequest::KEY_PC_WIDTH  => $pcWidth,
            BannerBaseRequest::KEY_SP_HEIGHT => $spHeight,
            BannerBaseRequest::KEY_SP_WIDTH  => $spWidth,
            BannerBaseRequest::KEY_START_AT  => $startAt,
            BannerBaseRequest::KEY_END_AT    => $endAt,
            BannerBaseRequest::KEY_URL       => $url,
        ], self::getHeaders());
        $response->assertStatus(StatusCodeMessages::STATUS_201);
    }

    /**
     * banner crerate 422 error data
     * @return array
     */
    public static function bannerCreate422FailedDataProvider(): array
    {
        self::createApplicationForStaticDataProvider();

        $caseKeys = [
            'no_name',
            'no_detail',
            'no_location',
            'no_pc_height',
            'no_pc_width',
            'no_sp_height',
            'no_sp_width',
            'no_start_at',
            'no_end_at',
            'start_at_grater_than_end_at',
            'start_at_invalid_format'
        ];
        $testCase = [];
        foreach ($caseKeys as $key) {
            $testCase[$key] = Config::get('myappTest.test.banners.create.success');
        }

        // データの整形
        $testCase['no_name'][BannerBaseRequest::KEY_NAME]           = '';
        $testCase['no_detail'][BannerBaseRequest::KEY_DETAIL]       = '';
        $testCase['no_location'][BannerBaseRequest::KEY_LOCATION]   = null;
        $testCase['no_pc_height'][BannerBaseRequest::KEY_PC_HEIGHT] = 0;
        $testCase['no_pc_width'][BannerBaseRequest::KEY_PC_WIDTH]   = 0;
        $testCase['no_sp_height'][BannerBaseRequest::KEY_SP_HEIGHT] = 0;
        $testCase['no_sp_width'][BannerBaseRequest::KEY_SP_WIDTH]   = 0;
        $testCase['no_start_at'][BannerBaseRequest::KEY_START_AT]   = '';
        $testCase['no_end_at'][BannerBaseRequest::KEY_END_AT]       = '';
        $testCase['start_at_grater_than_end_at'][BannerBaseRequest::KEY_START_AT] = '2031/08/20 00:00:00';
        $testCase['start_at_invalid_format'][BannerBaseRequest::KEY_START_AT] = '2022-05-20 00:00:00';

        return $testCase;
    }

    /**
     * banner create 422 error request test.
     * @dataProvider bannerCreate422FailedDataProvider
     * @return void
     */
    public function testCreateBanner422Failed(
        string $name,
        string $detail,
        ?string $location,
        int $pcHeight,
        int $pcWidth,
        int $spHeight,
        int $spWidth,
        string $startAt,
        string $endAt,
        string $url
    ): void {
        $response = $this->json('POST', route('admin.banners.create'), [
            BannerBaseRequest::KEY_NAME      => $name,
            BannerBaseRequest::KEY_DETAIL    => $detail,
            BannerBaseRequest::KEY_LOCATION  => $location,
            BannerBaseRequest::KEY_PC_HEIGHT => $pcHeight,
            BannerBaseRequest::KEY_PC_WIDTH  => $pcWidth,
            BannerBaseRequest::KEY_SP_HEIGHT => $spHeight,
            BannerBaseRequest::KEY_SP_WIDTH  => $spWidth,
            BannerBaseRequest::KEY_START_AT  => $startAt,
            BannerBaseRequest::KEY_END_AT    => $endAt,
            BannerBaseRequest::KEY_URL       => $url,
            // BannerBaseRequest::KEY_IMAGE    => $image,
        ], self::getHeaders());
        $response->assertStatus(StatusCodeMessages::STATUS_422);
    }

    /**
     * banners file download test.
     * output dir storage/framework/laravel-excel
     *
     * @return void
     */
    public function testDownloadBannersCsvFile(): void
    {
        $response = $this->get(route('admin.banners.download.csv'), self::getHeaders());
        $response->assertStatus(200)
            ->assertHeader('content-type', self::CONTENT_TYPE_TEXT_CSV_WITH_UTF8);
    }

    /**
     * banners template file download test.
     * output dir storage/framework/laravel-excel
     *
     * @return void
     */
    public function testDownloadBannersTemplateFile(): void
    {
        $response = $this->get(route('admin.banners.download.template'), self::getHeaders());
        $response->assertStatus(200)
            ->assertHeader('content-type', self::CONTENT_TYPE_APPLICATION_EXCEL);
    }

    /**
     * import banners filee request test.
     *
     * @return void
     */
    public function testImportBanners(): void
    {
        $name = Config::get('myappTest.test.banners.import.success')['fileName'];

        /* make file */
        // Symfony file package extends SplFileInfo
        $symfonyFile = Excel::download(
            new BannersBulkInsertTemplateExport(collect(Config::get('myappTest.test.banners.import.fileData'))),
            $name
        )->getFile();
        $file = UploadedFile::fake()->createWithContent($name, $symfonyFile->getContent());

        $response = $this->json('POST', route('admin.banners.upload.template'), [
            'file' => $file
        ], self::getHeaders());
        $response->assertStatus(201);
    }


    /**
     * banner update request test.
     *
     * @return void
     */
    public function testUpdateBanner(): void
    {
        // テスト用にID=1を指定
        $uuid = BannerLibrary::getTestBannerUuidByNumber(1);

        $response = $this->json('PATCH', route('admin.banners.update', [BannerBaseRequest::KEY_UUID => $uuid]), [
            BannerBaseRequest::KEY_NAME     => 'test banner name4',
            BannerBaseRequest::KEY_DETAIL   => 'test banner detail',
            BannerBaseRequest::KEY_LOCATION => 'home',
            BannerBaseRequest::KEY_PC_HEIGHT => 100,
            BannerBaseRequest::KEY_PC_WIDTH => 100,
            BannerBaseRequest::KEY_SP_HEIGHT => 100,
            BannerBaseRequest::KEY_SP_WIDTH => 100,
            BannerBaseRequest::KEY_START_AT => '2022/08/20 00:00:00',
            BannerBaseRequest::KEY_END_AT   => '2022/08/21 23:59:59',
            BannerBaseRequest::KEY_URL       => 'locahost/banner/test',
            // BannerBaseRequest::KEY_IMAGE    => null,
        ], self::getHeaders());
        $response->assertStatus(StatusCodeMessages::STATUS_200);
    }

    /**
     * banners update request failed test.
     *
     * @return void
     */
    public function testUpdateFailedBanner(): void
    {
        $uuid = BannerLibrary::getTestBannerUuidByNumber(1);

        $response = $this->json('PATCH', route('admin.banners.update', [BannerBaseRequest::KEY_UUID => $uuid]), [
            BannerBaseRequest::KEY_NAME     => 'test banner name4',
            BannerBaseRequest::KEY_DETAIL   => 'test banner detail',
            BannerBaseRequest::KEY_LOCATION => 0,
            BannerBaseRequest::KEY_PC_HEIGHT => 0,
            BannerBaseRequest::KEY_PC_WIDTH => 0,
            BannerBaseRequest::KEY_SP_HEIGHT => 0,
            BannerBaseRequest::KEY_SP_WIDTH => 0,
            BannerBaseRequest::KEY_START_AT => '2022/08/20 00:00:00',
            BannerBaseRequest::KEY_END_AT   => '2022/08/21 23:59:59',
            BannerBaseRequest::KEY_URL      => '',
            // BannerBaseRequest::KEY_IMAGE    => null,
        ], self::getHeaders());
        $response->assertStatus(StatusCodeMessages::STATUS_422);
    }

    /**
     * banner delete request test.
     * @return void
     */
    public function testRemoveBannerSuccess(): void
    {
        $uuid = BannerLibrary::getTestBannerUuidByNumber(1);

        $response = $this->json('DELETE', route('admin.banners.delete'), [
            BannerBaseRequest::KEY_BANNERS => [$uuid]
        ], self::getHeaders());
        $response->assertStatus(StatusCodeMessages::STATUS_200);
    }

    /**
     * banner delete data
     * @return array
     */
    public static function bannerRemoveValidationErrorDataProvider(): array
    {
        return [
            'no exist banners'            => [BannerBaseRequest::KEY_BANNERS => [100]],
            'not integer value in array' => [BannerBaseRequest::KEY_BANNERS => ['string']]
        ];
    }

    /**
     * banner remove validation error test.
     * @dataProvider bannerRemoveValidationErrorDataProvider
     * @return void
     */
    public function testRemoveBannerValidationError(array $banners): void
    {
        $response = $this->json('DELETE', route('admin.banners.delete'), [
            BannerBaseRequest::KEY_BANNERS => $banners
        ], self::getHeaders());
        $response->assertStatus(StatusCodeMessages::STATUS_422);
    }
}
