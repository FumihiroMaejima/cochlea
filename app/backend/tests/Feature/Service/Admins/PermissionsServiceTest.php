<?php

namespace Tests\Feature\Service\Admins;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Tests\ServiceBaseTestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use App\Library\Message\StatusCodeMessages;
use App\Http\Requests\BaseRequest;

// use Illuminate\Foundation\Testing\RefreshDatabase;
// use Illuminate\Foundation\Testing\DatabaseMigrations;

class PermissionsServiceTest extends ServiceBaseTestCase
{
    /**
     * setUpは各テストメソッドが実行される前に実行する
     * 親クラスのsetUpを必ず実行する
     */
    protected function setUp(): void
    {
        parent::setUp();
        $loginUser = [];

        // 各クラスで1回だけ行たい処理
        if (!$this->initialized) {
            $loginUser         = $this->setUpInit();
            $this->initialized = true;
        }

        $this->withHeaders([
            Config::get('myapp.headers.id')        => $loginUser[self::INIT_REQUEST_RESPONSE_USER_ID],
            Config::get('myapp.headers.authority') => $loginUser[self::INIT_REQUEST_RESPONSE_USER_AUTHORITY],
            Config::get('myapp.headers.authorization') => self::TOKEN_PREFIX . $loginUser[self::INIT_REQUEST_RESPONSE_TOKEN],
        ]);
    }

    /**
     * roles get request test.
     *
     * @return void
     */
    public function testGetPermissionsList(): void
    {
        $response = $this->get(route('admin.permissions.list'));
        $response->assertStatus(StatusCodeMessages::STATUS_200)
            ->assertJsonCount(4, BaseRequest::RESPONSE_KEY_DATA);
    }
}
