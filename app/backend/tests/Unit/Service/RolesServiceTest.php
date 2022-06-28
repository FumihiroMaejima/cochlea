<?php

namespace Tests\Unit\Service;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Tests\ServiceBaseTestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use Database\Seeders\Masters\AdminsTableSeeder;
use Database\Seeders\Masters\AdminsRolesTableSeeder;
use Database\Seeders\Masters\PermissionsTableSeeder;
use Database\Seeders\Masters\RolePermissionsTableSeeder;
use Database\Seeders\Masters\RolesTableSeeder;

// use Illuminate\Foundation\Testing\DatabaseMigrations;
// use Illuminate\Foundation\Testing\RefreshDatabase;

class RolesServiceTest extends ServiceBaseTestCase
{
    /**
     * setUpは各テストメソッドが実行される前に実行する
     * 親クラスのsetUpを必ず実行する
     */
    protected function setUp(): void
    {
        $this->seederClasses = [
            AdminsTableSeeder::class,
            PermissionsTableSeeder::class,
            RolesTableSeeder::class,
            RolePermissionsTableSeeder::class,
            AdminsRolesTableSeeder::class,
        ];

        parent::setUp();
        $loginUser = [];

        if (!$this->initialized) {
            $loginUser         = $this->init();
            $this->initialized = true;
        }

        $this->withHeaders([
            Config::get('myapp.headers.id')        => $loginUser[self::INIT_REQUEST_RESPONSE_USER_ID],
            Config::get('myapp.headers.authority') => $loginUser[self::INIT_REQUEST_RESPONSE_USER_AUTHORITY],
            Config::get('myapp.headers.authorization') => 'Bearer ' . $loginUser[self::INIT_REQUEST_RESPONSE_TOKEN],
        ]);
    }

    /**
     * roles get request test.
     *
     * @return void
     */
    public function testGetRoles(): void
    {
        $response = $this->get(route('admin.roles.index'));
        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    /**
     * roles list get request test.
     *
     * @return void
     */
    public function testGetRolesList(): void
    {
        $response = $this->get(route('admin.roles.list'));
        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    /**
     * role crerate data
     * @return array
     */
    public function roleCreateDataProvider(): array
    {
        $this->createApplication();

        return [
            'create role data' => Config::get('myappTest.test.roles.create.success')
        ];
    }

    /**
     * role create request test.
     * @dataProvider roleCreateDataProvider
     * @return void
     */
    public function testCreateRoleSuccess(string $name, string $code, string $detail, array $permissions): void
    {
        $response = $this->json('POST', route('admin.roles.create'), [
            'name'        => $name,
            'code'        => $code,
            'detail'      => $detail,
            'permissions' => $permissions
        ]);
        $response->assertStatus(201);
    }

    /**
     * role crerate 422 error data
     * @return array
     */
    public function roleCreate422FailedDataProvider(): array
    {
        $this->createApplication();

        $caseKeys = ['no_name', 'no_code', 'no_detail', 'no_permission', 'no_exist_permission'];
        $testCase = [];
        foreach ($caseKeys as $key) {
            $testCase[$key] = Config::get('myappTest.test.roles.create.success');
        }

        // データの整形
        $testCase['no_name']['name']                    = '';
        $testCase['no_code']['code']                    = '';
        $testCase['no_detail']['detail']                = '';
        $testCase['no_permission']['permissions']       = [];
        $testCase['no_exist_permission']['permissions'] = [5];

        return $testCase;
    }

    /**
     * role create 422 error request test.
     * @dataProvider roleCreate422FailedDataProvider
     * @return void
     */
    public function testCreateRole422Failed(string $name, string $code, string $detail, array $permissions): void
    {
        $response = $this->json('POST', route('admin.roles.create'), [
            'name'        => $name,
            'code'        => $code,
            'detail'      => $detail,
            'permissions' => $permissions
        ]);
        $response->assertStatus(422);
    }

    /**
     * roles file download test.
     * output dir storage/framework/laravel-excel
     *
     * @return void
     */
    public function testDownloadRolesCsvFile(): void
    {
        $response = $this->get(route('admin.roles.download'));
        $response->assertStatus(200)
            ->assertHeader('content-type', 'application/csv');
    }


    /**
     * roles update request test.
     *
     * @return void
     */
    public function testUpdateRoles(): void
    {
        $response = $this->json('PATCH', route('admin.roles.update', ['id' => 4]), [
            'name'        => 'test name',
            'code'        => 'test_code1',
            'detail'      => 'test detail',
            'permissions' => [2]
        ]);
        $response->assertStatus(200);
    }

    /**
     * roles update request failed test.
     *
     * @return void
     */
    public function testUpdateFailedRoles(): void
    {
        $response = $this->json('PATCH', route('admin.roles.update', ['id' => 4]), [
            'name'        => '',
            'code'        => 'test_code1',
            'detail'      => 'test detail',
            'permissions' => []
        ]);
        $response->assertStatus(422);
    }

    /**
     * role delete request test.
     * @return void
     */
    public function testRemoveRoleSuccess(): void
    {
        $response = $this->json('DELETE', route('admin.roles.delete'), [
            'roles' => [1]
        ]);
        $response->assertStatus(200);
    }

    /**
     * role delete data
     * @return array
     */
    public function roleRemoveValidationErrorDataProvider(): array
    {
        $this->createApplication();

        return [
            'no exist roles'             => ['roles' => [100]],
            'not integer value in array' => ['roles' => ['string']]
        ];
    }

    /**
     * role remove validation error test.
     * @dataProvider roleRemoveValidationErrorDataProvider
     * @return void
     */
    public function testRemoveMemberValidationError(array $roles): void
    {
        $response = $this->json('DELETE', route('admin.roles.delete'), [
            'roles' => $roles
        ]);
        $response->assertStatus(422);
    }
}
