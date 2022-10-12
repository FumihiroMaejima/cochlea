<?php

namespace Tests\Feature\Service\Users;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\WithFaker;
// use Illuminate\Foundation\Testing\DatabaseMigrations;
// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Trait\HelperTrait;
use Database\Seeders\UsersTableSeeder;
use Database\Seeders\Masters\AdminsTableSeeder;
use Database\Seeders\Masters\AdminsRolesTableSeeder;
use Database\Seeders\Masters\PermissionsTableSeeder;
use Database\Seeders\Masters\RolePermissionsTableSeeder;
use Database\Seeders\Masters\RolesTableSeeder;

/**
 * ユーザー用のServiceクラスのテスト用Baseクラス
 */
class UserServiceBaseTestCase extends TestCase
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
    protected const INIT_REQUEST_RESPONSE_USER_NAME = 'name';
    protected const INIT_REQUEST_RESPONSE_USER_AUTHORITY = 'user_authority';

    // token prefix
    protected const TOKEN_PREFIX = 'Bearer ';

    /** @var string CONNECTION_NAME_FOR_CI CIなどで使う場合のコネクション名。単一のコネクションに接続させる。 */
    private const CONNECTION_NAME_FOR_CI = 'sqlite';
    /** @var string CONNECTION_NAME_FOR_TESTING UnitTestで使う場合のコネクション名。単一のコネクションに接続させる。 */
    private const CONNECTION_NAME_FOR_TESTING = 'mysql_testing';

    protected $initialized = false;

    // 初期化の為のtruncateを行う対象のテーブル名
    protected array $refreshTables = [];

    // target seeders.
    protected array $seederClasses = [
        UsersTableSeeder::class,
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
        $logsConnectionName = Config::get('myapp.database.logs.baseConnectionName');
        $userConnectionName = Config::get('myapp.database.users.baseConnectionName');
        $connection = '';

        // connection 設定がCI用の設定の場合
        if (($logsConnectionName === self::CONNECTION_NAME_FOR_CI) && ($userConnectionName === self::CONNECTION_NAME_FOR_CI)) {
            $connection = self::CONNECTION_NAME_FOR_CI;
        } else {
            // テスト用DB内のテーブルの削除
            $connection = self::CONNECTION_NAME_FOR_TESTING;
        }

        // $this->artisan('db:wipe', ['--database' => $connection]);
        // $this->artisan('migrate:fresh');
        $this->artisan('testing:truncate', ['tables' => $this->refreshTables]);
        $this->seed($this->seederClasses);

        // ログインリクエスト
        $response = $this->json('POST', route('auth.user.login'), [
            'email'    => Config::get('myappTest.test.admin.login.email'),
            'password' => Config::get('myappTest.test.admin.login.password')
        ], ['Content-Type' => 'application/json'])->json();

        return [
            self::INIT_REQUEST_RESPONSE_TOKEN     => $response[self::LOGIN_RESEPONSE_KEY_ACCESS_TOKEN] ?? '',
            self::INIT_REQUEST_RESPONSE_USER_ID   => $response[self::LOGIN_RESEPONSE_KEY_USER][self::ADMIN_RESOURCE_KEY_ID] ?? 0,
            self::INIT_REQUEST_RESPONSE_USER_NAME => $response[self::LOGIN_RESEPONSE_KEY_USER][self::ADMIN_RESOURCE_KEY_NAME] ?? ''
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
