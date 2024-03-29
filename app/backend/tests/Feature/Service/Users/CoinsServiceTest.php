<?php

namespace Tests\Feature\Service\Users;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Tests\UserServiceBaseTestCase;
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
    protected const SEEDER_CLASSES = [
        CoinsTableSeeder::class,
        UsersTableSeeder::class,
    ];

    /**
     * setUpは各テストメソッドが実行される前に実行する
     * 親クラスのsetUpを必ず実行する
     */
    protected function setUp(): void
    {
        parent::setUp();

        // 各クラスで1回だけ行たい処理
        if (!static::$initialized) {
            // user系サービスの1番最初のテストのテストの為usersテーブルを初期化する
            $loginUser = $this->setUpInit(
                [
                    (new Coins())->getTable(),
                    (new User())->getTable(),
                ]
            );
            static::$initialized = true;

            $this->withHeaders([
                Config::get('myapp.headers.id')        => $loginUser[self::INIT_REQUEST_RESPONSE_USER_ID],
                Config::get('myapp.headers.authorization') => self::TOKEN_PREFIX . $loginUser[self::INIT_REQUEST_RESPONSE_TOKEN],
            ]);
        }
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
