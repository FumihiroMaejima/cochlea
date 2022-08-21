<?php

namespace Tests\Feature\Service;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Tests\ServiceBaseTestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\WithFaker;

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
     * admins get request test.
     *
     * @return void
     */
    public function testGetAdminss(): void
    {
        $response = $this->get(route('admin.admins.index'));
        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
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
    public function adminCreateDataProvider(): array
    {
        $this->createApplication();

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
            'name'                  => $name,
            'email'                 => $email,
            'roleId'                => $roleId,
            'password'              => $password,
            'password_confirmation' => $password_confirmation
        ]);
        $response->assertStatus(201);
    }

    /**
     * admin crerate 422 error data
     * @return array
     */
    public function adminCreate422FailedDataProvider(): array
    {
        $this->createApplication();

        $caseKeys = ['no_name', 'no_email', 'no_exist_role', 'no_password', 'no_password_confirmation', 'not_same_password'];

        $testCase = [];
        foreach ($caseKeys as $key) {
            $testCase[$key] = Config::get('myappTest.test.admin.create.success');
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
     * admins create 422 error request test.
     * @dataProvider adminCreate422FailedDataProvider
     * @return void
     */
    public function testCreateAdmin422Failed(string $name, string $email, int $roleId, string $password, string $password_confirmation): void
    {
        /* $data = Config::get('myappTest.test.admin.create.success');
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
     * admin file download test.
     * output dir storage/framework/laravel-excel
     *
     * @return void
     */
    public function testDownloadAdminsCsvFile(): void
    {
        $response = $this->get(route('admin.admins.download'));
        $response->assertStatus(200)
            ->assertHeader('content-type', self::CONTENT_TYPE_TEXT_CSV);
    }

    /**
     * admin update request test.
     *
     * @return void
     */
    public function testUpdateAdmins(): void
    {
        $response = $this->json('PATCH', route('admin.admins.update', ['id' => 1]), [
            'name'   => 'test name',
            'email'  => Config::get('myappTest.test.admin.login.email'),
            'roleId' => 1
        ]);
        $response->assertStatus(200);
    }

    /**
     * admin update request failed test.
     *
     * @return void
     */
    public function testUpdateFailedAdmin(): void
    {
        $response = $this->json('PATCH', route('admin.admins.update', ['id' => 1]), [
            'name'   => '',
            'email'  => Config::get('myappTest.test.admin.login.email'),
            'roleId' => 1
        ]);
        $response->assertStatus(422);
    }

    /**
     * admin remove data
     * @return array
     */
    public function adminRemoveDataProvider(): array
    {
        $this->createApplication();

        return [
            'id is 3' => ['id' => 3]
        ];
    }

    /**
     * admin delete request test.
     * @dataProvider adminRemoveDataProvider
     * @return void
     */
    public function testRemoveAdminsSuccess(int $id): void
    {
        $response = $this->json('DELETE', route('admin.admins.delete', ['id' => $id]));
        $response->assertStatus(200);
    }

    /**
     * admin delete data
     * @return array
     */
    public function adminRemoveValidationErrorDataProvider(): array
    {
        $this->createApplication();

        return [
            'no exist id' => ['id' => 100],
            'not inteder value' => ['id' => (int)('string value')]
        ];
    }

    /**
     * admin delete request test.
     * @dataProvider adminRemoveValidationErrorDataProvider
     * @return void
     */
    public function testRemoveAdminsValidationError(int $id): void
    {
        $response = $this->json('DELETE', route('admin.admins.delete', ['id' => $id]));
        $response->assertStatus(422);
    }
}
