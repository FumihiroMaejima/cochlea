<?php

namespace Tests\Feature\Service\Users;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Tests\UserServiceBaseTestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Requests\User\Informations\InformationBaseRequest;
use App\Library\Message\StatusCodeMessages;
use App\Models\Masters\Informations;
use App\Models\Users\UserReadInformations;
use App\Repositories\Users\UserReadInformations\UserReadInformationsRepository;
use Database\Seeders\Masters\InformationsTableSeeder;

// use Illuminate\Foundation\Testing\DatabaseMigrations;
// use Illuminate\Foundation\Testing\RefreshDatabase;

class InformationsServiceTest extends UserServiceBaseTestCase
{
    // target seeders.
    protected const SEEDER_CLASSES = [
        InformationsTableSeeder::class,
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
            $loginUser = $this->setUpInit(
                [
                    // (new Informations())->getTable(),
                    // (new UserReadInformationsRepository())->getTable(), // $userIdからshardIdを設定する必要がある。
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
     * informations get request test.
     *
     * @return void
     */
    public function testGetInformations(): void
    {
        $response = $this->get(route('noAuth.informations.index'));
        $response->assertStatus(StatusCodeMessages::STATUS_200)
            ->assertJsonCount(5, self::RESPONSE_KEY_DATA);
    }

    /**
     * user read information crerate data
     * @return array
     */
    public function userReadInformationCreateDataProvider(): array
    {
        $this->createApplication();

        return [
            'create user read information' => [
                InformationBaseRequest::KEY_ID => 1
            ]
        ];
    }

    /**
     * user reead information create request test.
     * @dataProvider userReadInformationCreateDataProvider
     * @return void
     */
    public function testCreateUserReadInformationSuccess(int $informationId): void
    {
        $response = $this->post(
            route('user.informations.information.read.create', [InformationBaseRequest::KEY_ID => $informationId]),
            headers: self::getHeaders()
        );
        $response->assertStatus(StatusCodeMessages::STATUS_201);
    }
}
