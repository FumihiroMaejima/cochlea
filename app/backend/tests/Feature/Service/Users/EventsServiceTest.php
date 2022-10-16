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
use App\Models\Masters\Events;
use Database\Seeders\Masters\EventsTableSeeder;

// use Illuminate\Foundation\Testing\DatabaseMigrations;
// use Illuminate\Foundation\Testing\RefreshDatabase;

class EventsServiceTest extends UserServiceBaseTestCase
{
    // target seeders.
    protected const SEEDER_CLASSES = [
        EventsTableSeeder::class,
    ];

    /**
     * setUpは各テストメソッドが実行される前に実行する
     * 親クラスのsetUpを必ず実行する
     */
    protected function setUp(): void
    {
        $this->refreshTables = [
            (new Events())->getTable(),
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
     * events get request test.
     *
     * @return void
     */
    public function testGetEvents(): void
    {
        $response = $this->get(route('noAuth.events.index'));
        $response->assertStatus(StatusCodeMessages::STATUS_200)
            ->assertJsonCount(5, self::RESPONSE_KEY_DATA);
    }
}
