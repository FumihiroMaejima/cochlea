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
use App\Exports\Masters\ServiceTerms\ServiceTermsBulkInsertTemplateExport;
use Database\Seeders\Masters\AdminsTableSeeder;
use Database\Seeders\Masters\AdminsRolesTableSeeder;
use Database\Seeders\Masters\PermissionsTableSeeder;
use Database\Seeders\Masters\RolePermissionsTableSeeder;
use Database\Seeders\Masters\RolesTableSeeder;
use Database\Seeders\Masters\ServiceTermsTableSeeder;

// use Illuminate\Foundation\Testing\DatabaseMigrations;
// use Illuminate\Foundation\Testing\RefreshDatabase;

class ServiceTermsServiceTest extends AdminServiceBaseTestCase
{
    // target seeders.
    protected const SEEDER_CLASSES = [
        AdminsTableSeeder::class,
        ServiceTermsTableSeeder::class,
        PermissionsTableSeeder::class,
        RolesTableSeeder::class,
        RolePermissionsTableSeeder::class,
        AdminsRolesTableSeeder::class,
    ];

    /**
     * service terms file download test.
     * output dir storage/framework/laravel-excel
     *
     * @return void
     */
    public function testDownloadInformationsCsvFile(): void
    {
        $response = $this->get(route('admin.serviceTerms.download.csv'), self::getHeaders());
        $response->assertStatus(200)
            ->assertHeader('content-type', self::CONTENT_TYPE_TEXT_CSV_WITH_UTF8);
    }

    /**
     * service terms template file download test.
     * output dir storage/framework/laravel-excel
     *
     * @return void
     */
    public function testDownloadInformationsTemplateFile(): void
    {
        $response = $this->get(route('admin.serviceTerms.download.template'), self::getHeaders());
        $response->assertStatus(200)
            ->assertHeader('content-type', self::CONTENT_TYPE_TEXT_CSV_WITH_UTF8);
    }

    /**
     * import service terms filee request test.
     *
     * @return void
     */
    public function testImportInformations(): void
    {
        $name = Config::get('myappTest.test.serviceTerms.import.success')['fileName'];

        /* make file */
        // Symfony file package extends SplFileInfo
        $symfonyFile = Excel::download(
            new ServiceTermsBulkInsertTemplateExport(collect(Config::get('myappTest.test.serviceTerms.import.fileData'))),
            $name
        )->getFile();
        $file = UploadedFile::fake()->createWithContent($name, $symfonyFile->getContent());

        $response = $this->json('POST', route('admin.serviceTerms.upload.template'), [
            'file' => $file
        ], self::getHeaders());
        $response->assertStatus(201);
    }
}
