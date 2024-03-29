<?php

namespace Tests;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\WithFaker;
// use Illuminate\Foundation\Testing\DatabaseMigrations;
// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Trait\HelperTrait;
use Database\Seeders\UsersTableSeeder;

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

    // init() response key
    protected const INIT_REQUEST_RESPONSE_TOKEN = 'token';
    protected const INIT_REQUEST_RESPONSE_USER_ID = 'user_id';
    protected const INIT_REQUEST_RESPONSE_USER_NAME = 'name';
    protected const INIT_REQUEST_RESPONSE_USER_AUTHORITY = 'user_authority';

    // token prefix
    protected const TOKEN_PREFIX = 'Bearer ';

    // response keys
    protected const RESPONSE_KEY_DATA = 'data';

    // content-type
    protected const CONTENT_TYPE_APPLICATION_CSV = 'application/csv';
    protected const CONTENT_TYPE_TEXT_CSV = 'text/csv';
    protected const CONTENT_TYPE_TEXT_CSV_WITH_UTF8 = 'text/csv; charset=UTF-8';
    protected const CONTENT_TYPE_APPLICATION_EXCEL = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

    // target seeders.
    /** @var array<int, Seeder> SEEDER_CLASSES insert予定のシーダーファイル  */
    protected const SEEDER_CLASSES = [
        UsersTableSeeder::class,
    ];

    /** @var static bool $initialized either initialized.  */
    protected static bool $initialized = false;

    /** @var static int $id user id.  */
    protected static int $id = 0;
    /** @var static string $authorization session.  */
    protected static string $authorization = '';

    /**
     * This method is called before the first test of this test class is run.
     * Can't using Laravel methods.
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // 初期化済みフラグの解消
        static::$initialized = false;
        // セッション等の初期化など
    }

    /**
     * setup初期化処理
     * (setUpBeforeClass()で行いたいがArtisanコマンドなどが実行出来ないので各クラスのsetup()で1回だけ利用する。)
     *
     * @param array<int string> $tables refresh target tables
     * @return array
     */
    protected function setUpInit(array $tables = []): array
    {
        // $this->artisan('db:wipe', ['--database' => $connection]);
        // $this->artisan('migrate:fresh');
        // $this->artisan('testing:truncate', ['tables' => $this->refreshTables]);
        // $this->seed(static::SEEDER_CLASSES);

        if (!empty($tables)) {
            Artisan::call('testing:truncate', ['tables' => $tables]);
        }
        foreach (static::SEEDER_CLASSES as $className) {
            Artisan::call('db:seed', ['--class' => $className, '--no-interaction' => true]);
        }

        // ログインリクエスト
        $response = $this->login();

        // 認証ヘッダー設定
        self::setHeaders(
            $response[self::LOGIN_RESEPONSE_KEY_USER][self::ADMIN_RESOURCE_KEY_ID] ?? 0,
            self::TOKEN_PREFIX . ($response[self::LOGIN_RESEPONSE_KEY_ACCESS_TOKEN] ?? '')
        );

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

    /**
     * login request.
     *
     * @return array
     */
    protected function login(): array
    {
        // ログインリクエスト
        return $this->json('POST', route('auth.user.login'), [
            'email'    => Config::get('myappTest.test.user.login.email'),
            'password' => Config::get('myappTest.test.user.login.password')
        ], ['Content-Type' => 'application/json'])->json();
    }

    /**
     * get headers.
     *
     * @return array
     */
    protected static function getHeaders(): array
    {
        // ログインリクエスト
        return [
            Config::get('myapp.headers.id') => static::$id,
            Config::get('myapp.headers.authorization') => static::$authorization,
        ];
    }

    /**
     * set headers.
     *
     * @param int $id
     * @param string $authorization
     * @return void
     */
    protected static function setHeaders(int $id, string $authorization): void
    {
        static::$id = $id;
        static::$authorization = $authorization;
    }
}
