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
use Database\Seeders\Masters\ServiceTermsTableSeeder;
use Database\Seeders\UsersTableSeeder;

// use Illuminate\Foundation\Testing\DatabaseMigrations;
// use Illuminate\Foundation\Testing\RefreshDatabase;

class ServiceTermsServiceTest extends UserServiceBaseTestCase
{
    // target seeders.
    protected const SEEDER_CLASSES = [
        ServiceTermsTableSeeder::class,
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
        if (!$this->initialized) {
            // user系サービスの1番最初のテストのテストの為usersテーブルを初期化する
            $loginUser = $this->setUpInit(
                [
                    (new Coins())->getTable(),
                    (new User())->getTable(),
                ]
            );
            $this->initialized = true;

            $this->withHeaders([
                Config::get('myapp.headers.id')        => $loginUser[self::INIT_REQUEST_RESPONSE_USER_ID],
                Config::get('myapp.headers.authorization') => self::TOKEN_PREFIX . $loginUser[self::INIT_REQUEST_RESPONSE_TOKEN],
            ]);
        }
    }

    /**
     * noAuth latest service terms get request test.
     *
     * @return void
     */
    public function testGetServiceTerms(): void
    {
        $response = $this->get(route('noAuth.serviceTerms.index'));
        // idカラムの数を加算してチェック
        $response->assertStatus(StatusCodeMessages::STATUS_200)
            ->assertJsonCount(count(ServiceTermsTableSeeder::TEMPALTE) + 1, self::RESPONSE_KEY_DATA);
    }
}
