<?php

namespace Tests;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\WithFaker;
// use Illuminate\Foundation\Testing\DatabaseMigrations;
// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Trait\HelperTrait;
use Database\Seeders\Masters\AdminsTableSeeder;
use Database\Seeders\Masters\AdminsRolesTableSeeder;
use Database\Seeders\Masters\PermissionsTableSeeder;
use Database\Seeders\Masters\RolePermissionsTableSeeder;
use Database\Seeders\Masters\RolesTableSeeder;

/**
 * Serviceクラスのテスト用Baseクラス
 */
class ServiceBaseTestCase extends TestCase
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

        // DB内のテーブルの削除
        $this->artisan('db:wipe', ['--database' => 'mysql']);
        $this->artisan('db:wipe', ['--database' => 'mysql_logs']);
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
    }
}
