<?php

namespace Tests\Unit\Service;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Trait\HelperTrait;
use Database\Seeders\AdminsTableSeeder;
use Database\Seeders\AdminsRolesTableSeeder;
use Database\Seeders\PermissionsTableSeeder;
use Database\Seeders\RolePermissionsTableSeeder;
use Database\Seeders\RolesTableSeeder;
use App\Models\Admins;

class AdminsServiceTest extends TestCase
{
    use HelperTrait;
    // use DatabaseMigrations;
    // use RefreshDatabase;

    protected $initialized = false;

    /**
     * 初期化処理
     *
     * @return array
     */
    protected function init(): array
    {
        // $this->refreshDatabase();
        // $this->refreshTestDatabase();
        // $this->runDatabaseMigrations();
        // $this->seed([AdminsTableSeeder::class])->init();

        $this->artisan('migrate:fresh');
        $this->seed([
            AdminsTableSeeder::class,
            PermissionsTableSeeder::class,
            RolesTableSeeder::class,
            RolePermissionsTableSeeder::class,
            AdminsRolesTableSeeder::class,
        ]);

        $response = $this->json('POST', route('auth.admin.login'), [
            'email'    => Config::get('myapp.test.admin.login.email'),
            'password' => Config::get('myapp.test.admin.login.password')
        ], ['Content-Type' => 'application/json'])->json();

        // echo var_dump($response) . "\n";

        return [
            'token'          => $response['access_token'],
            'user_id'        => $response['user']['id'],
            'user_authority' => $response['user']['authority']
        ];
    }

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
            'X-Auth-ID'        => $loginUser['user_id'],
            'X-Auth-Authority' => $loginUser['user_authority'],
            'Authorization'    => 'Bearer ' . $loginUser['token'],
        ]);
    }

    /**
     * members get request test.
     *
     * @return void
     */
    public function testGetMembers(): void
    {
        $response = $this->get(route('admin.admins.index'));
        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    /**
     * member data
     * @param int id
     * @param string name
     * @param string email
     * @param int roleId
     */
    public function memberDataProvider(): array
    {
        return [
            'member' => [1, 'test name', Config::get('myapp.test.admin.login.email'), 1]
        ];
    }

    /**
     * member crerate data
     * @return array
     */
    public function memberCreateDataProvider(): array
    {
        $this->createApplication();

        return [
            'create member data' => Config::get('myapp.test.member.create.success')
        ];
    }

    /**
     * members create request test.
     * @dataProvider memberCreateDataProvider
     * @return void
     */
    public function testCreateMemberSuccess(string $name, string $email, int $roleId, string $password, string $password_confirmation): void
    {
        $response = $this->json('POST', route('admin.admins.create'), [
            'name'                  => $name,
            'email'                 => $email,
            'roleId'                => $roleId,
            'password'              => $password,
            'password_confirmation' => $password_confirmation
        ]);
        $response->assertStatus(201);
    }

    /**
     * member crerate 422 error data
     * @return array
     */
    public function memberCreate422FailedDataProvider(): array
    {
        $this->createApplication();

        $caseKeys = ['no_name', 'no_email', 'no_exist_role', 'no_password', 'no_password_confirmation', 'not_same_password'];

        $testCase = [];
        foreach ($caseKeys as $key) {
            $testCase[$key] = Config::get('myapp.test.member.create.success');
        }

        // データの整形
        $testCase['no_name']['name']                                   = '';
        $testCase['no_email']['email']                                 = '';
        $testCase['no_exist_role']['roleId']                           = 0;
        $testCase['no_password']['password']                           = '';
        $testCase['no_password_confirmation']['password_confirmation'] = '';
        $testCase['not_same_password']['password_confirmation']        = '1234';

        return $testCase;
    }

    /**
     * members create 422 error request test.
     * @dataProvider memberCreate422FailedDataProvider
     * @return void
     */
    public function testCreateMember422Failed(string $name, string $email, int $roleId, string $password, string $password_confirmation): void
    {
        /* $data = Config::get('myapp.test.member.create.success');
        $data['name'] = ''; */
        $response = $this->json('POST', route('admin.admins.create'), [
            'name'                  => $name,
            'email'                 => $email,
            'roleId'                => $roleId,
            'password'              => $password,
            'password_confirmation' => $password_confirmation
        ]);
        $response->assertStatus(422);
    }


    /**
     * members file download test.
     * output dir storage/framework/laravel-excel
     *
     * @return void
     */
    public function testDownloadMembersCsvFile(): void
    {
        $response = $this->get(route('admin.admins.download'));
        $response->assertStatus(200)
            ->assertHeader('content-type', 'application/csv');
    }

    /**
     * members update request test.
     *
     * @return void
     */
    public function testUpdateMembers(): void
    {
        $response = $this->json('PATCH', route('admin.admins.update', ['id' => 1]), [
            'name'   => 'test name',
            'email'  => Config::get('myapp.test.admin.login.email'),
            'roleId' => 1
        ]);
        $response->assertStatus(200);
    }

    /**
     * members update request failed test.
     *
     * @return void
     */
    public function testUpdateFailedMembers(): void
    {
        $response = $this->json('PATCH', route('admin.admins.update', ['id' => 1]), [
            'name'   => '',
            'email'  => Config::get('myapp.test.admin.login.email'),
            'roleId' => 1
        ]);
        $response->assertStatus(422);
    }

    /**
     * member crerate data
     * @return array
     */
    public function memberRemoveDataProvider(): array
    {
        $this->createApplication();

        return [
            'id is 3' => ['id' => 3]
        ];
    }

    /**
     * members delete request test.
     * @dataProvider memberRemoveDataProvider
     * @return void
     */
    public function testRemoveMemberSuccess(int $id): void
    {
        $response = $this->json('DELETE', route('admin.admins.delete', ['id' => $id]));
        $response->assertStatus(200);
    }

    /**
     * member delete data
     * @return array
     */
    public function memberRemoveValidationErrorDataProvider(): array
    {
        $this->createApplication();

        return [
            'no exist id' => ['id' => 100],
            'not inteder value' => ['id' => (int)('string value')]
        ];
    }

    /**
     * members delete request test.
     * @dataProvider memberRemoveValidationErrorDataProvider
     * @return void
     */
    public function testRemoveMemberValidationError(int $id): void
    {
        $response = $this->json('DELETE', route('admin.admins.delete', ['id' => $id]));
        $response->assertStatus(422);
    }
}
