<?php

namespace Tests\Unit\Service;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Tests\ServiceBaseTestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use App\Trait\HelperTrait;
use Database\Seeders\Masters\AdminsTableSeeder;
use Database\Seeders\Masters\AdminsRolesTableSeeder;
use Database\Seeders\Masters\PermissionsTableSeeder;
use Database\Seeders\Masters\RolePermissionsTableSeeder;
use Database\Seeders\Masters\RolesTableSeeder;

// use Illuminate\Foundation\Testing\DatabaseMigrations;
// use Illuminate\Foundation\Testing\RefreshDatabase;

class RolesServiceTest extends TestCase
{
    use HelperTrait;

    // login response
    protected const LOGIN_RESEPONSE_KEY_ACCESS_TOKEN = 'access_token';
    protected const LOGIN_RESEPONSE_KEY_TOKEN_TYPE = 'token_type';
    protected const LOGIN_RESEPONSE_KEY_EXPIRES_IN = 'expires_in';
    protected const LOGIN_RESEPONSE_KEY_USER = 'user';

    // admin resource key
    protected const ADMIN_RESOURCE_KEY_ID = 'id';
    protected const ADMIN_RESOURCE_KEY_NAME = 'name';
    protected const ADMIN_RESOURCE_KEY_AUTHORITY = 'authority';

    // init() response key
    protected const INIT_REQUEST_RESPONSE_TOKEN = 'token';
    protected const INIT_REQUEST_RESPONSE_USER_ID = 'user_id';
    protected const INIT_REQUEST_RESPONSE_USER_AUTHORITY = 'user_authority';

    /** @var string CONNECTION_NAME_FOR_CI CIなどで使う場合のコネクション名。単一のコネクションに接続させる。 */
    private const CONNECTION_NAME_FOR_CI = 'sqlite';

    protected $initialized = false;

    // target seeders.
    protected array $seederClasses = [
        AdminsTableSeeder::class,
        PermissionsTableSeeder::class,
        RolesTableSeeder::class,
        RolePermissionsTableSeeder::class,
        AdminsRolesTableSeeder::class,
    ];

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

        $logsConnectionName = Config::get('myapp.database.logs.baseConnectionName');
        $userConnectionName = Config::get('myapp.database.users.baseConnectionName');

        // connection 設定がデフォルトの場合
        if (($logsConnectionName === self::CONNECTION_NAME_FOR_CI) && ($userConnectionName === self::CONNECTION_NAME_FOR_CI)) {
            $this->artisan('db:wipe', ['--database' => self::CONNECTION_NAME_FOR_CI]);
        } else {
            // DB内のテーブルの削除
            $this->artisan('db:wipe', ['--database' => 'mysql']);
            $this->artisan('db:wipe', ['--database' => 'mysql_logs']);
            $this->artisan('db:wipe', ['--database' => 'mysql_user1']);
            $this->artisan('db:wipe', ['--database' => 'mysql_user2']);
            $this->artisan('db:wipe', ['--database' => 'mysql_user3']);
        }

        $this->artisan('migrate:fresh');
        $this->seed($this->seederClasses);

        // ログインリクエスト
        $response = $this->json('POST', route('auth.admin.login'), [
            'email'    => Config::get('myappTest.test.admin.login.email'),
            'password' => Config::get('myappTest.test.admin.login.password')
        ], ['Content-Type' => 'application/json'])->json();

        return [
            self::INIT_REQUEST_RESPONSE_TOKEN          => $response[self::LOGIN_RESEPONSE_KEY_ACCESS_TOKEN],
            self::INIT_REQUEST_RESPONSE_USER_ID        => $response[self::LOGIN_RESEPONSE_KEY_USER][self::ADMIN_RESOURCE_KEY_ID],
            self::INIT_REQUEST_RESPONSE_USER_AUTHORITY => $response[self::LOGIN_RESEPONSE_KEY_USER][self::ADMIN_RESOURCE_KEY_AUTHORITY]
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
