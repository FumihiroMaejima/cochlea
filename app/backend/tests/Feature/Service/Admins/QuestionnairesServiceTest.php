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
use App\Exports\Masters\Questionnaires\QuestionnairesBulkInsertTemplateExport;
use Database\Seeders\Masters\AdminsTableSeeder;
use Database\Seeders\Masters\AdminsRolesTableSeeder;
use Database\Seeders\Masters\PermissionsTableSeeder;
use Database\Seeders\Masters\QuestionnairesTableSeeder;
use Database\Seeders\Masters\RolePermissionsTableSeeder;
use Database\Seeders\Masters\RolesTableSeeder;

// use Illuminate\Foundation\Testing\DatabaseMigrations;
// use Illuminate\Foundation\Testing\RefreshDatabase;

class QuestionnairesServiceTest extends AdminServiceBaseTestCase
{
    // target seeders.
    protected const SEEDER_CLASSES = [
        AdminsTableSeeder::class,
        PermissionsTableSeeder::class,
        QuestionnairesTableSeeder::class,
        RolesTableSeeder::class,
        RolePermissionsTableSeeder::class,
        AdminsRolesTableSeeder::class,
    ];

    /**
     * questionnaires file download test.
     * output dir storage/framework/laravel-excel
     *
     * @return void
     */
    public function testDownloadQuestionnairesCsvFile(): void
    {
        $response = $this->get(route('admin.questionnaires.download.csv'), self::getHeaders());
        $response->assertStatus(200)
            ->assertHeader('content-type', self::CONTENT_TYPE_TEXT_CSV_WITH_UTF8);
    }

    /**
     * questionnaires template file download test.
     * output dir storage/framework/laravel-excel
     *
     * @return void
     */
    public function testDownloadQuestionnairesTemplateFile(): void
    {
        $response = $this->get(route('admin.questionnaires.download.template'), self::getHeaders());
        $response->assertStatus(200)
            ->assertHeader('content-type', self::CONTENT_TYPE_TEXT_CSV_WITH_UTF8);
    }

    /**
     * import questionnaires filee request test.
     *
     * @return void
     */
    public function testImportQuestionnaires(): void
    {
        $name = Config::get('myappTest.test.questionnaires.import.success')['fileName'];

        /* make file */
        // Symfony file package extends SplFileInfo
        $symfonyFile = Excel::download(
            new QuestionnairesBulkInsertTemplateExport(collect(Config::get('myappTest.test.questionnaires.import.fileData'))),
            $name
        )->getFile();
        $file = UploadedFile::fake()->createWithContent($name, $symfonyFile->getContent());

        $response = $this->json('POST', route('admin.questionnaires.upload.template'), [
            'file' => $file
        ], self::getHeaders());
        $response->assertStatus(201);
    }
}
