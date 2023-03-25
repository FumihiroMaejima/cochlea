<?php

namespace Tests\Feature\Service\Users;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Tests\UserServiceBaseTestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use App\Library\Message\StatusCodeMessages;
use App\Models\Masters\Banners;
use App\Models\Masters\Events;
use Database\Seeders\Masters\BannersTableSeeder;
use Database\Seeders\Masters\EventsTableSeeder;

// use Illuminate\Foundation\Testing\DatabaseMigrations;
// use Illuminate\Foundation\Testing\RefreshDatabase;

class BannerServiceTest extends UserServiceBaseTestCase
{
    // target seeders.
    protected const SEEDER_CLASSES = [
        BannersTableSeeder::class,
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
            $loginUser = $this->setUpInit(
                [
                    (new Banners())->getTable(),
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
     * banners get request test.
     *
     * @return void
     */
    public function testGetBanners(): void
    {
        $response = $this->get(route('noAuth.banners.index'));
        $response->assertStatus(StatusCodeMessages::STATUS_200)
            ->assertJsonCount(5, self::RESPONSE_KEY_DATA);
    }
}
