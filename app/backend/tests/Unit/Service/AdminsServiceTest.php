<?php

namespace Tests\Unit\Service;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Tests\ServiceBaseTestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\WithFaker;
use Database\Seeders\Masters\AdminsTableSeeder;
use Database\Seeders\Masters\AdminsRolesTableSeeder;
use Database\Seeders\Masters\PermissionsTableSeeder;
use Database\Seeders\Masters\RolePermissionsTableSeeder;
use Database\Seeders\Masters\RolesTableSeeder;

// use Illuminate\Foundation\Testing\DatabaseMigrations;
// use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminsServiceTest extends ServiceBaseTestCase
{
    // use DatabaseMigrations;
    // use RefreshDatabase;

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

        // Config::get('myapp.headers.authority')
        $this->withHeaders([
            Config::get('myapp.headers.id')        => $loginUser[self::INIT_REQUEST_RESPONSE_USER_ID],
            Config::get('myapp.headers.authority') => $loginUser[self::INIT_REQUEST_RESPONSE_USER_AUTHORITY],
            Config::get('myapp.headers.authorization') => 'Bearer ' . $loginUser[self::INIT_REQUEST_RESPONSE_TOKEN],
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
            'member' => [1, 'test name', Config::get('myappTest.test.admin.login.email'), 1]
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
            'create member data' => Config::get('myappTest.test.member.create.success')
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
            $testCase[$key] = Config::get('myappTest.test.member.create.success');
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
        /* $data = Config::get('myappTest.test.member.create.success');
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
            'email'  => Config::get('myappTest.test.admin.login.email'),
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
            'email'  => Config::get('myappTest.test.admin.login.email'),
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
