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
use App\Exports\Masters\Events\EventsBulkInsertTemplateExport;
use App\Http\Requests\Admin\Events\EventBaseRequest;
use Database\Seeders\Masters\AdminsTableSeeder;
use Database\Seeders\Masters\AdminsRolesTableSeeder;
use Database\Seeders\Masters\EventsTableSeeder;
use Database\Seeders\Masters\PermissionsTableSeeder;
use Database\Seeders\Masters\RolePermissionsTableSeeder;
use Database\Seeders\Masters\RolesTableSeeder;

// use Illuminate\Foundation\Testing\DatabaseMigrations;
// use Illuminate\Foundation\Testing\RefreshDatabase;

class EventsServiceTest extends AdminServiceBaseTestCase
{
    // target seeders.
    protected const SEEDER_CLASSES = [
        AdminsTableSeeder::class,
        EventsTableSeeder::class,
        PermissionsTableSeeder::class,
        RolesTableSeeder::class,
        RolePermissionsTableSeeder::class,
        AdminsRolesTableSeeder::class,
    ];

    /**
     * events get request test.
     *
     * @return void
     */
    public function testGetEvents(): void
    {
        $response = $this->get(route('admin.events.index'), self::getHeaders());
        $response->assertStatus(StatusCodeMessages::STATUS_200)
            ->assertJsonCount(5, self::RESPONSE_KEY_DATA);
    }

    /**
     * event crerate data
     * @return array
     */
    public function eventCreateDataProvider(): array
    {
        $this->createApplication();

        return [
            'create event data' => Config::get('myappTest.test.events.create.success')
        ];
    }

    /**
     * event create request test.
     * @dataProvider eventCreateDataProvider
     * @return void
     */
    public function testCreateEventSuccess(
        string $name,
        int $type,
        string $detail,
        string $startAt,
        string $endAt
    ): void {
        $response = $this->json('POST', route('admin.events.create'), [
            EventBaseRequest::KEY_NAME     => $name,
            EventBaseRequest::KEY_TYPE     => $type,
            EventBaseRequest::KEY_DETAIL   => $detail,
            EventBaseRequest::KEY_START_AT => $startAt,
            EventBaseRequest::KEY_END_AT   => $endAt,
        ], self::getHeaders());
        $response->assertStatus(StatusCodeMessages::STATUS_201);
    }

    /**
     * event crerate 422 error data
     * @return array
     */
    public function eventCreate422FailedDataProvider(): array
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
            $testCase[$key] = Config::get('myappTest.test.events.create.success');
        }

        // データの整形
        $testCase['no_name'][EventBaseRequest::KEY_NAME]               = '';
        $testCase['no_type'][EventBaseRequest::KEY_TYPE]               = null;
        $testCase['no_detail'][EventBaseRequest::KEY_DETAIL]           = '';
        $testCase['no_start_at'][EventBaseRequest::KEY_START_AT]       = '';
        $testCase['no_end_at'][EventBaseRequest::KEY_END_AT]           = '';
        $testCase['start_at_grater_than_end_at'][EventBaseRequest::KEY_START_AT] = '2031/08/20 00:00:00';
        $testCase['start_at_invalid_format'][EventBaseRequest::KEY_START_AT] = '2022-05-20 00:00:00';

        return $testCase;
    }

    /**
     * event create 422 error request test.
     * @dataProvider eventCreate422FailedDataProvider
     * @return void
     */
    public function testCreateEvent422Failed(
        string $name,
        int|null $type,
        string $detail,
        string $startAt,
        string $endAt
    ): void {
        $response = $this->json('POST', route('admin.events.create'), [
            EventBaseRequest::KEY_NAME     => $name,
            EventBaseRequest::KEY_TYPE     => $type,
            EventBaseRequest::KEY_DETAIL   => $detail,
            EventBaseRequest::KEY_START_AT => $startAt,
            EventBaseRequest::KEY_END_AT   => $endAt,
            // EventBaseRequest::KEY_IMAGE    => $image,
        ], self::getHeaders());
        $response->assertStatus(StatusCodeMessages::STATUS_422);
    }

    /**
     * events file download test.
     * output dir storage/framework/laravel-excel
     *
     * @return void
     */
    public function testDownloadEventsCsvFile(): void
    {
        $response = $this->get(route('admin.events.download.csv'), self::getHeaders());
        $response->assertStatus(200)
            ->assertHeader('content-type', self::CONTENT_TYPE_TEXT_CSV_WITH_UTF8);
    }

    /**
     * events template file download test.
     * output dir storage/framework/laravel-excel
     *
     * @return void
     */
    public function testDownloadEventsTemplateFile(): void
    {
        $response = $this->get(route('admin.events.download.template'), self::getHeaders());
        $response->assertStatus(200)
            ->assertHeader('content-type', self::CONTENT_TYPE_APPLICATION_EXCEL);
    }

    /**
     * import events filee request test.
     *
     * @return void
     */
    public function testImportEvents(): void
    {
        $name = Config::get('myappTest.test.events.import.success')['fileName'];

        /* make file */
        // Symfony file package extends SplFileInfo
        $symfonyFile = Excel::download(
            new EventsBulkInsertTemplateExport(collect(Config::get('myappTest.test.events.import.fileData'))),
            $name
        )->getFile();
        $file = UploadedFile::fake()->createWithContent($name, $symfonyFile->getContent());

        $response = $this->json('POST', route('admin.events.upload.template'), [
            'file' => $file
        ], self::getHeaders());
        $response->assertStatus(201);
    }


    /**
     * event update request test.
     *
     * @return void
     */
    public function testUpdateEvent(): void
    {
        $response = $this->json('PATCH', route('admin.events.update', [EventBaseRequest::KEY_ID => 4]), [
            EventBaseRequest::KEY_NAME     => 'test event name4',
            EventBaseRequest::KEY_TYPE     => 3,
            EventBaseRequest::KEY_DETAIL   => 'test event detail',
            EventBaseRequest::KEY_START_AT => '2022/08/20 00:00:00',
            EventBaseRequest::KEY_END_AT   => '2022/08/21 23:59:59',
            // EventBaseRequest::KEY_IMAGE    => null,
        ], self::getHeaders());
        $response->assertStatus(StatusCodeMessages::STATUS_200);
    }

    /**
     * events update request failed test.
     *
     * @return void
     */
    public function testUpdateFailedEvent(): void
    {
        $response = $this->json('PATCH', route('admin.events.update', [EventBaseRequest::KEY_ID => 4]), [
            EventBaseRequest::KEY_NAME     => 'test event name4',
            EventBaseRequest::KEY_TYPE     => 4,
            EventBaseRequest::KEY_DETAIL   => 'test event detail',
            EventBaseRequest::KEY_START_AT => '2022/08/20 00:00:00',
            EventBaseRequest::KEY_END_AT   => '2022/08/21 23:59:59',
            // EventBaseRequest::KEY_IMAGE    => null,
        ], self::getHeaders());
        $response->assertStatus(StatusCodeMessages::STATUS_422);
    }

    /**
     * event delete request test.
     * @return void
     */
    public function testRemoveEventSuccess(): void
    {
        $response = $this->json('DELETE', route('admin.events.delete'), [
            EventBaseRequest::KEY_EVENTS => [1]
        ], self::getHeaders());
        $response->assertStatus(StatusCodeMessages::STATUS_200);
    }

    /**
     * event delete data
     * @return array
     */
    public function eventRemoveValidationErrorDataProvider(): array
    {
        $this->createApplication();

        return [
            'no exist events'            => [EventBaseRequest::KEY_EVENTS => [100]],
            'not integer value in array' => [EventBaseRequest::KEY_EVENTS => ['string']]
        ];
    }

    /**
     * event remove validation error test.
     * @dataProvider eventRemoveValidationErrorDataProvider
     * @return void
     */
    public function testRemoveEventValidationError(array $events): void
    {
        $response = $this->json('DELETE', route('admin.events.delete'), [
            EventBaseRequest::KEY_EVENTS => $events
        ], self::getHeaders());
        $response->assertStatus(StatusCodeMessages::STATUS_422);
    }
}
