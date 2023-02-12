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
use App\Library\Database\ShardingLibrary;
use App\Library\File\FileLibrary;
use App\Trait\HelperTrait;
use Database\Seeders\Masters\AdminsTableSeeder;
use Database\Seeders\Masters\AdminsRolesTableSeeder;
use Database\Seeders\Masters\PermissionsTableSeeder;
use Database\Seeders\Masters\RolePermissionsTableSeeder;
use Database\Seeders\Masters\RolesTableSeeder;

/**
 * 管理用Serviceクラスのテスト用Baseクラス
 */
class AdminServiceBaseTestCase extends TestCase
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

    // response keys
    protected const RESPONSE_KEY_DATA = 'data';

    // content-type
    protected const CONTENT_TYPE_APPLICATION_CSV = 'application/csv';
    protected const CONTENT_TYPE_TEXT_CSV = 'text/csv';
    protected const CONTENT_TYPE_TEXT_CSV_WITH_UTF8 = 'text/csv; charset=UTF-8';
    protected const CONTENT_TYPE_APPLICATION_EXCEL = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

    /** @var array<int, string> $refreshTables 初期化の為のtruncateを行う対象のテーブル名  */
    protected array $refreshTables = [];

    // target seeders.
    /** @var array<int, Seeder> SEEDER_CLASSES insert予定のシーダーファイル  */
    protected const SEEDER_CLASSES = [
        AdminsTableSeeder::class,
        PermissionsTableSeeder::class,
        RolesTableSeeder::class,
        RolePermissionsTableSeeder::class,
        AdminsRolesTableSeeder::class,
    ];

    /** @var static bool $initialized whichever initialized.  */
    protected static bool $initialized = false;

    /** @var static int $id user id.  */
    protected static int $id = 0;
    /** @var static string $authority autorities.  */
    protected static string $authority = '';
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
        // セッション等の初期化
        self::setHeaders(0, '', '');
    }

    /**
     * setup初期化処理
     * (setUpBeforeClass()で行いたいがArtisanコマンドなどが実行出来ないので各クラスのsetup()で1回だけ利用する。)
     *
     * @param bool $isWipe whichever wipe database.
     * @return array
     */
    protected function setUpInit(bool $isWipe = false): array
    {
        // $this->refreshDatabase();
        // $this->refreshTestDatabase();
        // $this->runDatabaseMigrations();

        // connection設定がCI用かテスト用DB内かの判定
        $connection = ShardingLibrary::getSingleConnectionByConfig();

        // $this->artisan('db:wipe', ['--database' => $connection]);
        // $this->artisan('migrate:fresh');
        // $this->seed(static::SEEDER_CLASSES);
        if ($isWipe) {
            Artisan::call('db:wipe', ['--database' => $connection]);
        }
        Artisan::call('migrate:fresh', ['--database' => $connection]);
        foreach (static::SEEDER_CLASSES as $className) {
            Artisan::call('db:seed', ['--class' => $className, '--no-interaction' => true]);
        }

        // 既存ファイルの削除(ディレクトリごと削除)
        $directory = Config::get('myappFile.upload.storage.local.teting.session');
        $result = FileLibrary::deleteDeletectory($directory);

        // ログインリクエスト
        $response = $this->login();

        return [
            self::INIT_REQUEST_RESPONSE_TOKEN          => $response[self::LOGIN_RESEPONSE_KEY_ACCESS_TOKEN] ?? '',
            self::INIT_REQUEST_RESPONSE_USER_ID        => $response[self::LOGIN_RESEPONSE_KEY_USER][self::ADMIN_RESOURCE_KEY_ID] ?? 0,
            self::INIT_REQUEST_RESPONSE_USER_AUTHORITY => $response[self::LOGIN_RESEPONSE_KEY_USER][self::ADMIN_RESOURCE_KEY_AUTHORITY] ?? ''
        ];
    }

    /**
     * setUpは各テストメソッドが実行される前に実行する
     * 親クラスのsetUpを必ず実行する
     */
    protected function setUp(): void
    {
        parent::setUp();

        // 各クラスで1回だけ行たい処理
        if (!static::$initialized) {
            $loginUser = $this->setUpInit(true);
            $loginAuthoriey = is_array($loginUser[self::INIT_REQUEST_RESPONSE_USER_AUTHORITY])
            ? implode(',', $loginUser[self::INIT_REQUEST_RESPONSE_USER_AUTHORITY])
            : '';

            self::setHeaders(
                $loginUser[self::INIT_REQUEST_RESPONSE_USER_ID],
                $loginAuthoriey,
                self::TOKEN_PREFIX . $loginUser[self::INIT_REQUEST_RESPONSE_TOKEN]
            );

            static::$initialized = true;
        }
    }

    /**
     * login request.
     *
     * @return array
     */
    protected function login(): array
    {
        // ログインリクエスト
        return $this->json('POST', route('auth.admin.login'), [
            'email'    => Config::get('myappTest.test.admin.login.email'),
            'password' => Config::get('myappTest.test.admin.login.password')
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
            Config::get('myapp.headers.id')        => static::$id,
            Config::get('myapp.headers.authority') => static::$authority,
            Config::get('myapp.headers.authorization') => static::$authorization,
        ];
    }

    /**
     * set headers.
     *
     * @param int $id
     * @param string $authority
     * @param string $authorization
     * @return void
     */
    protected static function setHeaders(int $id, string $authority, string $authorization): void
    {
        static::$id        = $id;
        static::$authority = $authority;
        static::$authorization = $authorization;
    }
}
