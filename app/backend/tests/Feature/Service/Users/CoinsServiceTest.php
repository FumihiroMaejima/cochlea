<?php

namespace Tests\Feature\Service\Users;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Tests\ServiceBaseTestCase;
use Tests\Feature\Service\Users\UserServiceBaseTestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use App\Library\Message\StatusCodeMessages;
use App\Models\Masters\Coins;
use App\Models\User;
use Database\Seeders\Masters\CoinsTableSeeder;
use Database\Seeders\UsersTableSeeder;

// use Illuminate\Foundation\Testing\DatabaseMigrations;
// use Illuminate\Foundation\Testing\RefreshDatabase;

class CoinsServiceTest extends UserServiceBaseTestCase
{
    // target seeders.
    protected array $seederClasses = [
        CoinsTableSeeder::class,
        UsersTableSeeder::class,
    ];

    /**
     * setUpは各テストメソッドが実行される前に実行する
     * 親クラスのsetUpを必ず実行する
     */
    protected function setUp(): void
    {
        // user系サービスの1番最初のテストのテストの為usersテーブルを初期化する
        $this->refreshTables = [
            (new Coins())->getTable(),
            (new User())->getTable(),
        ];
        parent::setUp();
        $loginUser = [];

        if (!$this->initialized) {
            $loginUser         = $this->init();
            $this->initialized = true;
        }

        $this->withHeaders([
            Config::get('myapp.headers.id')        => $loginUser[self::INIT_REQUEST_RESPONSE_USER_ID],
            Config::get('myapp.headers.authorization') => self::TOKEN_PREFIX . $loginUser[self::INIT_REQUEST_RESPONSE_TOKEN],
        ]);
    }

    /**
     * noAuth coins get request test.
     *
     * @return void
     */
    public function testGetCoins(): void
    {
        $response = $this->get(route('noAuth.coins.index'));
        $response->assertStatus(StatusCodeMessages::STATUS_200)
            ->assertJsonCount(5, self::RESPONSE_KEY_DATA);
    }
}
