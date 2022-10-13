<?php

namespace Tests;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\WithFaker;
// use Illuminate\Foundation\Testing\DatabaseMigrations;
// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Library\Database\ShardingLibrary;
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

    // token prefix
    protected const TOKEN_PREFIX = 'Bearer ';

    protected $initialized = false;

    /** @var array<int, string> $refreshTables 初期化の為のtruncateを行う対象のテーブル名  */
    protected array $refreshTables = [];

    // target seeders.
    /** @var array<int, Seeder> $refreshTables insert予定のシーダーファイル  */
    protected array $seederClasses = [
        AdminsTableSeeder::class,
        PermissionsTableSeeder::class,
        RolesTableSeeder::class,
        RolePermissionsTableSeeder::class,
        AdminsRolesTableSeeder::class,
    ];

    // response keys
    protected const RESPONSE_KEY_DATA = 'data';


    protected const CONTENT_TYPE_APPLICATION_CSV = 'application/csv';
    protected const CONTENT_TYPE_TEXT_CSV = 'text/csv';
    protected const CONTENT_TYPE_APPLICATION_EXCEL = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

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

        // connection設定がCI用かテスト用DB内かの判定
        $connection = ShardingLibrary::getSingleConnectionByConfig();

        $this->artisan('db:wipe', ['--database' => $connection]);
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
