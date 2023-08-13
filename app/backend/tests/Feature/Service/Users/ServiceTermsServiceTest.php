<?php

namespace Tests\Feature\Service\Users;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Tests\UserServiceBaseTestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Requests\User\ServiceTerms\UserServiceTermsCreateRequest;
use App\Library\Message\StatusCodeMessages;
use App\Models\Masters\ServiceTerms;
use Database\Seeders\Masters\ServiceTermsTableSeeder;

// use Illuminate\Foundation\Testing\DatabaseMigrations;
// use Illuminate\Foundation\Testing\RefreshDatabase;

class ServiceTermsServiceTest extends UserServiceBaseTestCase
{
    // target seeders.
    protected const SEEDER_CLASSES = [
        ServiceTermsTableSeeder::class,
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
                    (new ServiceTerms())->getTable(),
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

    /**
     * user service term crerate data
     * @return array
     */
    public function createUserServiceTermDataProvider(): array
    {
        $this->createApplication();

        return [
            'create user service term error: not latest serivce term' => [
                UserServiceTermsCreateRequest::KEY_ID => 1,
                'expect' => StatusCodeMessages::STATUS_404,
            ],
            'create user service term success: latest serivce term' => [
                UserServiceTermsCreateRequest::KEY_ID => 5, // ServiceTermsTableSeeder::SEEDER_DATA_TESTING_LENGTH
                'expect' => StatusCodeMessages::STATUS_201,
            ],
        ];
    }

    /**
     * user service term create request test.
     * @dataProvider createUserServiceTermDataProvider
     * @return void
     */
    public function testCreateUserServiceTermSuccess(int $serviceTermId, int $expect): void
    {
        $response = $this->post(
            route(
                'user.serviceTerms.serviceTerm.agree.create',
                [UserServiceTermsCreateRequest::KEY_ID => $serviceTermId]
            ),
            [],
            self::getHeaders()
        );
        $response->assertStatus($expect);
    }
}
