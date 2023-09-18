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
use App\Library\Message\StatusCodeMessages;
use App\Exports\Masters\Informations\InformationsBulkInsertTemplateExport;
use App\Http\Requests\Admin\Informations\InformationBaseRequest;
use Database\Seeders\Masters\AdminsTableSeeder;
use Database\Seeders\Masters\AdminsRolesTableSeeder;
use Database\Seeders\Masters\InformationsTableSeeder;
use Database\Seeders\Masters\PermissionsTableSeeder;
use Database\Seeders\Masters\RolePermissionsTableSeeder;
use Database\Seeders\Masters\RolesTableSeeder;

// use Illuminate\Foundation\Testing\DatabaseMigrations;
// use Illuminate\Foundation\Testing\RefreshDatabase;

class InformationsServiceTest extends AdminServiceBaseTestCase
{
    // target seeders.
    protected const SEEDER_CLASSES = [
        AdminsTableSeeder::class,
        InformationsTableSeeder::class,
        PermissionsTableSeeder::class,
        RolesTableSeeder::class,
        RolePermissionsTableSeeder::class,
        AdminsRolesTableSeeder::class,
    ];

    /**
     * informations get request test.
     *
     * @return void
     */
    public function testGetInformations(): void
    {
        $response = $this->get(route('admin.informations.index'), self::getHeaders());
        $response->assertStatus(StatusCodeMessages::STATUS_200)
            ->assertJsonCount(5, self::RESPONSE_KEY_DATA);
    }

    /**
     * information crerate data
     * @return array
     */
    public function informationCreateDataProvider(): array
    {
        $this->createApplication();

        return [
            'create information data' => Config::get('myappTest.test.informations.create.success')
        ];
    }

    /**
     * information create request test.
     * @dataProvider informationCreateDataProvider
     * @return void
     */
    public function testCreateInformationSuccess(
        string $name,
        int $type,
        string $detail,
        string $startAt,
        string $endAt
    ): void {
        $response = $this->json('POST', route('admin.informations.create'), [
            InformationBaseRequest::KEY_NAME     => $name,
            InformationBaseRequest::KEY_TYPE     => $type,
            InformationBaseRequest::KEY_DETAIL   => $detail,
            InformationBaseRequest::KEY_START_AT => $startAt,
            InformationBaseRequest::KEY_END_AT   => $endAt,
            // InformationBaseRequest::KEY_IMAGE    => $image,
        ], self::getHeaders());
        $response->assertStatus(StatusCodeMessages::STATUS_201);
    }

    /**
     * information crerate 422 error data
     * @return array
     */
    public function informationCreate422FailedDataProvider(): array
    {
        $this->createApplication();

        $caseKeys = [
            'no_name',
            'no_type',
            'no_detail',
            'no_start_at',
            'no_end_at',
            'start_at_grater_than_end_at',
            'start_at_invalid_format'
        ];
        $testCase = [];
        foreach ($caseKeys as $key) {
            $testCase[$key] = Config::get('myappTest.test.informations.create.success');
        }

        // データの整形
        $testCase['no_name'][InformationBaseRequest::KEY_NAME]               = '';
        $testCase['no_type'][InformationBaseRequest::KEY_TYPE]               = null;
        $testCase['no_detail'][InformationBaseRequest::KEY_DETAIL]           = '';
        $testCase['no_start_at'][InformationBaseRequest::KEY_START_AT]       = '';
        $testCase['no_end_at'][InformationBaseRequest::KEY_END_AT]           = '';
        $testCase['start_at_grater_than_end_at'][InformationBaseRequest::KEY_START_AT] = '2031/08/20 00:00:00';
        $testCase['start_at_invalid_format'][InformationBaseRequest::KEY_START_AT] = '2022-05-20 00:00:00';

        return $testCase;
    }

    /**
     * information create 422 error request test.
     * @dataProvider informationCreate422FailedDataProvider
     * @return void
     */
    public function testCreateInformation422Failed(
        string $name,
        int|null $type,
        string $detail,
        string $startAt,
        string $endAt
    ): void {
        $response = $this->json('POST', route('admin.informations.create'), [
            InformationBaseRequest::KEY_NAME     => $name,
            InformationBaseRequest::KEY_TYPE     => $type,
            InformationBaseRequest::KEY_DETAIL   => $detail,
            InformationBaseRequest::KEY_START_AT => $startAt,
            InformationBaseRequest::KEY_END_AT   => $endAt,
            // InformationBaseRequest::KEY_IMAGE    => $image,
        ], self::getHeaders());
        $response->assertStatus(StatusCodeMessages::STATUS_422);
    }

    /**
     * informations file download test.
     * output dir storage/framework/laravel-excel
     *
     * @return void
     */
    public function testDownloadInformationsCsvFile(): void
    {
        $response = $this->get(route('admin.informations.download.csv'), self::getHeaders());
        $response->assertStatus(200)
            ->assertHeader('content-type', self::CONTENT_TYPE_TEXT_CSV_WITH_UTF8);
    }

    /**
     * informations template file download test.
     * output dir storage/framework/laravel-excel
     *
     * @return void
     */
    public function testDownloadInformationsTemplateFile(): void
    {
        $response = $this->get(route('admin.informations.download.template'), self::getHeaders());
        $response->assertStatus(200)
            ->assertHeader('content-type', self::CONTENT_TYPE_APPLICATION_EXCEL);
    }

    /**
     * import informations filee request test.
     *
     * @return void
     */
    public function testImportInformations(): void
    {
        $name = Config::get('myappTest.test.informations.import.success')['fileName'];

        /* make file */
        // Symfony file package extends SplFileInfo
        $symfonyFile = Excel::download(
            new InformationsBulkInsertTemplateExport(collect(Config::get('myappTest.test.informations.import.fileData'))),
            $name
        )->getFile();
        $file = UploadedFile::fake()->createWithContent($name, $symfonyFile->getContent());

        $response = $this->json('POST', route('admin.informations.upload.template'), [
            'file' => $file
        ], self::getHeaders());
        $response->assertStatus(201);
    }


    /**
     * information update request test.
     *
     * @return void
     */
    public function testUpdateInformation(): void
    {
        $response = $this->json('PATCH', route('admin.informations.update', [InformationBaseRequest::KEY_ID => 4]), [
            InformationBaseRequest::KEY_NAME     => 'test information name4',
            InformationBaseRequest::KEY_TYPE     => 3,
            InformationBaseRequest::KEY_DETAIL   => 'test information detail',
            InformationBaseRequest::KEY_START_AT => '2022/08/20 00:00:00',
            InformationBaseRequest::KEY_END_AT   => '2022/08/21 23:59:59',
            // InformationBaseRequest::KEY_IMAGE    => null,
        ], self::getHeaders());
        $response->assertStatus(StatusCodeMessages::STATUS_200);
    }

    /**
     * informations update request failed test.
     *
     * @return void
     */
    public function testUpdateFailedInformation(): void
    {
        $response = $this->json('PATCH', route('admin.informations.update', [InformationBaseRequest::KEY_ID => 4]), [
            InformationBaseRequest::KEY_NAME     => 'test information name4',
            InformationBaseRequest::KEY_TYPE     => 4,
            InformationBaseRequest::KEY_DETAIL   => 'test information detail',
            InformationBaseRequest::KEY_START_AT => '2022/08/20 00:00:00',
            InformationBaseRequest::KEY_END_AT   => '2022/08/21 23:59:59',
            // InformationBaseRequest::KEY_IMAGE    => null,
        ], self::getHeaders());
        $response->assertStatus(StatusCodeMessages::STATUS_422);
    }

    /**
     * information delete request test.
     * @return void
     */
    public function testRemoveInformationSuccess(): void
    {
        $response = $this->json('DELETE', route('admin.informations.delete'), [
            InformationBaseRequest::KEY_INFORMATIONS => [1]
        ], self::getHeaders());
        $response->assertStatus(StatusCodeMessages::STATUS_200);
    }

    /**
     * information delete data
     * @return array
     */
    public static function informationRemoveValidationErrorDataProvider(): array
    {
        return [
            'no exist informations'      => [InformationBaseRequest::KEY_INFORMATIONS => [100]],
            'not integer value in array' => [InformationBaseRequest::KEY_INFORMATIONS => ['string']]
        ];
    }

    /**
     * information remove validation error test.
     * @dataProvider informationRemoveValidationErrorDataProvider
     * @return void
     */
    public function testRemoveInformationValidationError(array $informations): void
    {
        $response = $this->json('DELETE', route('admin.informations.delete'), [
            InformationBaseRequest::KEY_INFORMATIONS => $informations
        ], self::getHeaders());
        $response->assertStatus(StatusCodeMessages::STATUS_422);
    }
}
