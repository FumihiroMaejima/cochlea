<?php

namespace Tests\Feature\Service\Admins;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Tests\AdminServiceBaseTestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use App\Library\Message\StatusCodeMessages;
use App\Http\Requests\Admin\Roles\RoleBaseRequest;

// use Illuminate\Foundation\Testing\DatabaseMigrations;
// use Illuminate\Foundation\Testing\RefreshDatabase;

class RolesServiceTest extends AdminServiceBaseTestCase
{
    /**
     * roles get request test.
     *
     * @return void
     */
    public function testGetRoles(): void
    {
        $response = $this->get(route('admin.roles.index'), self::getHeaders());
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
        $response = $this->get(route('admin.roles.list'), self::getHeaders());
        $response->assertStatus(StatusCodeMessages::STATUS_200)
            ->assertJsonCount(5, RoleBaseRequest::RESPONSE_KEY_DATA);
    }

    /**
     * role crerate data
     * @return array
     */
    public static function roleCreateDataProvider(): array
    {
        self::createApplicationForStaticDataProvider();

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
        ], self::getHeaders());
        $response->assertStatus(StatusCodeMessages::STATUS_201);
    }

    /**
     * role crerate 422 error data
     * @return array
     */
    public static function roleCreate422FailedDataProvider(): array
    {
        self::createApplicationForStaticDataProvider();

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
        ], self::getHeaders());
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
        $response = $this->get(route('admin.roles.download'), self::getHeaders());
        $response->assertStatus(StatusCodeMessages::STATUS_200)
            ->assertHeader('content-type', self::CONTENT_TYPE_TEXT_CSV_WITH_UTF8);
    }


    /**
     * role update request test.
     *
     * @return void
     */
    public function testUpdateRole(): void
    {
        $response = $this->json('PATCH', route('admin.roles.update', [RoleBaseRequest::KEY_ID => 4]), [
            RoleBaseRequest::KEY_NAME        => 'test name',
            RoleBaseRequest::KEY_CODE        => 'test_code1',
            RoleBaseRequest::KEY_DETAIL      => 'test detail',
            RoleBaseRequest::KEY_PERMISSIONS => [2]
        ], self::getHeaders());
        $response->assertStatus(StatusCodeMessages::STATUS_200);
    }

    /**
     * role update request failed test.
     *
     * @return void
     */
    public function testUpdateFailedRole(): void
    {
        $response = $this->json('PATCH', route('admin.roles.update', [RoleBaseRequest::KEY_ID => 4]), [
            RoleBaseRequest::KEY_NAME        => '',
            RoleBaseRequest::KEY_CODE        => 'test_code1',
            RoleBaseRequest::KEY_DETAIL      => 'test detail',
            RoleBaseRequest::KEY_PERMISSIONS => []
        ], self::getHeaders());
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
        ], self::getHeaders());
        $response->assertStatus(StatusCodeMessages::STATUS_200);
    }

    /**
     * role delete data
     * @return array
     */
    public static function roleRemoveValidationErrorDataProvider(): array
    {
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
    public function testRemoveRoleValidationError(array $roles): void
    {
        $response = $this->json('DELETE', route('admin.roles.delete'), [
            RoleBaseRequest::KEY_ROLES => $roles
        ], self::getHeaders());
        $response->assertStatus(StatusCodeMessages::STATUS_422);
    }
}
