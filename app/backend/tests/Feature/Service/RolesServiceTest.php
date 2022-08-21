<?php

namespace Tests\Feature\Service;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Tests\ServiceBaseTestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use App\Library\Message\StatusCodeMessages;
use App\Http\Requests\Admin\Roles\RoleBaseRequest;

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
     * roles get request test.
     *
     * @return void
     */
    public function testGetRoles(): void
    {
        $response = $this->get(route('admin.roles.index'));
        $response->assertStatus(StatusCodeMessages::STATUS_200)
            ->assertJsonCount(5, RoleBaseRequest::RESPONSE_KEY_DATA);
    }

    /**
     * roles list get request test.
     *
     * @return void
     */
    public function testGetRolesList(): void
    {
        $response = $this->get(route('admin.roles.list'));
        $response->assertStatus(StatusCodeMessages::STATUS_200)
            ->assertJsonCount(5, RoleBaseRequest::RESPONSE_KEY_DATA);
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
            RoleBaseRequest::KEY_NAME        => $name,
            RoleBaseRequest::KEY_CODE        => $code,
            RoleBaseRequest::KEY_DETAIL      => $detail,
            RoleBaseRequest::KEY_PERMISSIONS => $permissions
        ]);
        $response->assertStatus(StatusCodeMessages::STATUS_201);
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
        $testCase['no_name'][RoleBaseRequest::KEY_NAME]                    = '';
        $testCase['no_code'][RoleBaseRequest::KEY_CODE]                    = '';
        $testCase['no_detail'][RoleBaseRequest::KEY_DETAIL]                = '';
        $testCase['no_permission'][RoleBaseRequest::KEY_PERMISSIONS]       = [];
        $testCase['no_exist_permission'][RoleBaseRequest::KEY_PERMISSIONS] = [5];

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
            RoleBaseRequest::KEY_NAME        => $name,
            RoleBaseRequest::KEY_CODE        => $code,
            RoleBaseRequest::KEY_DETAIL      => $detail,
            RoleBaseRequest::KEY_PERMISSIONS => $permissions
        ]);
        $response->assertStatus(StatusCodeMessages::STATUS_422);
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
        $response->assertStatus(StatusCodeMessages::STATUS_200)
            ->assertHeader('content-type', self::CONTENT_TYPE_TEXT_CSV);
    }


    /**
     * roles update request test.
     *
     * @return void
     */
    public function testUpdateRoles(): void
    {
        $response = $this->json('PATCH', route('admin.roles.update', ['id' => 4]), [
            RoleBaseRequest::KEY_NAME        => 'test name',
            RoleBaseRequest::KEY_CODE        => 'test_code1',
            RoleBaseRequest::KEY_DETAIL      => 'test detail',
            RoleBaseRequest::KEY_PERMISSIONS => [2]
        ]);
        $response->assertStatus(StatusCodeMessages::STATUS_200);
    }

    /**
     * roles update request failed test.
     *
     * @return void
     */
    public function testUpdateFailedRoles(): void
    {
        $response = $this->json('PATCH', route('admin.roles.update', ['id' => 4]), [
            RoleBaseRequest::KEY_NAME        => '',
            RoleBaseRequest::KEY_CODE        => 'test_code1',
            RoleBaseRequest::KEY_DETAIL      => 'test detail',
            RoleBaseRequest::KEY_PERMISSIONS => []
        ]);
        $response->assertStatus(StatusCodeMessages::STATUS_422);
    }

    /**
     * role delete request test.
     * @return void
     */
    public function testRemoveRoleSuccess(): void
    {
        $response = $this->json('DELETE', route('admin.roles.delete'), [
            RoleBaseRequest::KEY_ROLES => [1]
        ]);
        $response->assertStatus(StatusCodeMessages::STATUS_200);
    }

    /**
     * role delete data
     * @return array
     */
    public function roleRemoveValidationErrorDataProvider(): array
    {
        $this->createApplication();

        return [
            'no exist roles'             => [RoleBaseRequest::KEY_ROLES => [100]],
            'not integer value in array' => [RoleBaseRequest::KEY_ROLES => ['string']]
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
            RoleBaseRequest::KEY_ROLES => $roles
        ]);
        $response->assertStatus(StatusCodeMessages::STATUS_422);
    }
}
