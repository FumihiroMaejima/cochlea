<?php

namespace Tests\Feature\Service\Admins;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Tests\AdminServiceBaseTestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\WithFaker;
use App\Library\Message\StatusCodeMessages;
use App\Http\Requests\Admin\Admins\AdminBaseRequest;

// use Illuminate\Foundation\Testing\DatabaseMigrations;
// use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminsServiceTest extends AdminServiceBaseTestCase
{
    // use DatabaseMigrations;
    // use RefreshDatabase;

    /**
     * setUpは各テストメソッドが実行される前に実行する
     * 親クラスのsetUpを必ず実行する
     */
    /* protected function setUp(): void
    {
        parent::setUp();

        // 各クラスで1回だけ行たい処理
        if (!$this->initialized) {
            $loginUser         = $this->setUpInit(true);
            $this->initialized = true;

            $this->withHeaders([
                Config::get('myapp.headers.id')        => $loginUser[self::INIT_REQUEST_RESPONSE_USER_ID],
                Config::get('myapp.headers.authority') => $loginUser[self::INIT_REQUEST_RESPONSE_USER_AUTHORITY],
                Config::get('myapp.headers.authorization') => self::TOKEN_PREFIX . $loginUser[self::INIT_REQUEST_RESPONSE_TOKEN],
            ]);
        }
    } */

    /**
     * admins get request test.
     *
     * @return void
     */
    public function testGetAdmins(): void
    {
        $response = $this->get(route('admin.admins.index'), self::getHeaders());
        $response->assertStatus(StatusCodeMessages::STATUS_200)
            ->assertJsonCount(5, AdminBaseRequest::RESPONSE_KEY_DATA);
    }

    /**
     * admin data
     * @param int id
     * @param string name
     * @param string email
     * @param int roleId
     */
    public function adminDataProvider(): array
    {
        return [
            'admin' => [1, 'test name', Config::get('myappTest.test.admin.login.email'), 1]
        ];
    }

    /**
     * admin crerate data
     * @return array
     */
    public static function adminCreateDataProvider(): array
    {
        self::createApplicationForStaticDataProvider();

        return [
            'create admin data' => Config::get('myappTest.test.admin.create.success')
        ];
    }

    /**
     * admin create request test.
     * @dataProvider adminCreateDataProvider
     * @return void
     */
    public function testCreateAdminsSuccess(string $name, string $email, int $roleId, string $password, string $password_confirmation): void
    {
        $response = $this->json('POST', route('admin.admins.create'), [
            AdminBaseRequest::KEY_NAME                  => $name,
            AdminBaseRequest::KEY_EMAIL                 => $email,
            AdminBaseRequest::KEY_ROLE_ID               => $roleId,
            AdminBaseRequest::KEY_PASSWORD              => $password,
            AdminBaseRequest::KEY_PASSWORD_CONFIRMATION => $password_confirmation
        ], self::getHeaders());
        $response->assertStatus(StatusCodeMessages::STATUS_201);
    }

    /**
     * admin crerate 422 error data
     * @return array
     */
    public static function adminCreate422FailedDataProvider(): array
    {
        self::createApplicationForStaticDataProvider();

        $caseKeys = ['no_name', 'no_email', 'no_exist_role', 'no_password', 'no_password_confirmation', 'not_same_password'];

        $testCase = [];
        foreach ($caseKeys as $key) {
            $testCase[$key] = Config::get('myappTest.test.admin.create.success');
        }

        // データの整形
        $testCase['no_name'][AdminBaseRequest::KEY_NAME]                                   = '';
        $testCase['no_email'][AdminBaseRequest::KEY_EMAIL]                                 = '';
        $testCase['no_exist_role'][AdminBaseRequest::KEY_ROLE_ID]                          = 0;
        $testCase['no_password'][AdminBaseRequest::KEY_PASSWORD]                           = '';
        $testCase['no_password_confirmation'][AdminBaseRequest::KEY_PASSWORD_CONFIRMATION] = '';
        $testCase['not_same_password'][AdminBaseRequest::KEY_PASSWORD_CONFIRMATION]        = '1234';

        return $testCase;
    }

    /**
     * admins create 422 error request test.
     * @dataProvider adminCreate422FailedDataProvider
     * @return void
     */
    public function testCreateAdmin422Failed(string $name, string $email, int $roleId, string $password, string $password_confirmation): void
    {
        /* $data = Config::get('myappTest.test.admin.create.success');
        $data['name'] = ''; */
        $response = $this->json('POST', route('admin.admins.create'), [
            AdminBaseRequest::KEY_NAME                  => $name,
            AdminBaseRequest::KEY_EMAIL                 => $email,
            AdminBaseRequest::KEY_ROLE_ID               => $roleId,
            AdminBaseRequest::KEY_PASSWORD              => $password,
            AdminBaseRequest::KEY_PASSWORD_CONFIRMATION => $password_confirmation
        ], self::getHeaders());
        $response->assertStatus(StatusCodeMessages::STATUS_422);
    }


    /**
     * admin file download test.
     * output dir storage/framework/laravel-excel
     *
     * @return void
     */
    public function testDownloadAdminsCsvFile(): void
    {
        $response = $this->get(route('admin.admins.download'), self::getHeaders());
        $response->assertStatus(StatusCodeMessages::STATUS_200)
            ->assertHeader('content-type', self::CONTENT_TYPE_TEXT_CSV_WITH_UTF8);
    }

    /**
     * admin update request test.
     *
     * @return void
     */
    public function testUpdateAdmins(): void
    {
        $response = $this->json('PATCH', route('admin.admins.update', [AdminBaseRequest::KEY_ID => 1]), [
            AdminBaseRequest::KEY_NAME    => 'test name',
            AdminBaseRequest::KEY_EMAIL   => Config::get('myappTest.test.admin.login.email'),
            AdminBaseRequest::KEY_ROLE_ID => 1
        ], self::getHeaders());
        $response->assertStatus(StatusCodeMessages::STATUS_200);
    }

    /**
     * admin update request failed test.
     *
     * @return void
     */
    public function testUpdateFailedAdmin(): void
    {
        $response = $this->json('PATCH', route('admin.admins.update', [AdminBaseRequest::KEY_ID => 1]), [
            AdminBaseRequest::KEY_NAME    => '',
            AdminBaseRequest::KEY_EMAIL   => Config::get('myappTest.test.admin.login.email'),
            AdminBaseRequest::KEY_ROLE_ID => 1
        ], self::getHeaders());
        $response->assertStatus(StatusCodeMessages::STATUS_422);
    }

    /**
     * admin remove data
     * @return array
     */
    public static function adminRemoveDataProvider(): array
    {
        return [
            'id is 3' => [AdminBaseRequest::KEY_ID => 3]
        ];
    }

    /**
     * admin delete request test.
     * @dataProvider adminRemoveDataProvider
     * @return void
     */
    public function testRemoveAdminsSuccess(int $id): void
    {
        // $response = $this->json('DELETE', route('admin.admins.delete', [AdminBaseRequest::KEY_ID => $id]), [], self::getHeaders());
        $response = $this->delete(route('admin.admins.delete', [AdminBaseRequest::KEY_ID => $id]), [], self::getHeaders());
        $response->assertStatus(StatusCodeMessages::STATUS_200);
    }

    /**
     * admin delete data
     * @return array
     */
    public static function adminRemoveValidationErrorDataProvider(): array
    {
        return [
            'no exist id' => [AdminBaseRequest::KEY_ID => 100],
            'not inteder value' => [AdminBaseRequest::KEY_ID => (int)('string value')]
        ];
    }

    /**
     * admin delete request test.
     * @dataProvider adminRemoveValidationErrorDataProvider
     * @return void
     */
    public function testRemoveAdminsValidationError(int $id): void
    {
        // $response = $this->json('DELETE', route('admin.admins.delete', [AdminBaseRequest::KEY_ID => $id]), [], self::getHeaders());
        $response = $this->delete(route('admin.admins.delete', [AdminBaseRequest::KEY_ID => $id]), [], self::getHeaders());
        $response->assertStatus(StatusCodeMessages::STATUS_422);
    }
}
