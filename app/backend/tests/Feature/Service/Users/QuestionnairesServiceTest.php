<?php

namespace Tests\Feature\Service\Users;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Tests\UserServiceBaseTestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Requests\User\Questionnaires\UserQuestionnairesCreateRequest;
use App\Library\Message\StatusCodeMessages;
use App\Models\Masters\Questionnaires;
use Database\Seeders\Masters\QuestionnairesTableSeeder;

// use Illuminate\Foundation\Testing\DatabaseMigrations;
// use Illuminate\Foundation\Testing\RefreshDatabase;

class QuestionnairesServiceTest extends UserServiceBaseTestCase
{
    // target seeders.
    protected const SEEDER_CLASSES = [
        QuestionnairesTableSeeder::class,
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
                    (new Questionnaires())->getTable(),
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
     * noAuth latest questionnaires get request test.
     *
     * @return void
     */
    public function testGetQuestionnaires(): void
    {
        $response = $this->get(route('noAuth.questionnaires.index'));
        // idカラムの数を加算してチェック
        $response->assertStatus(StatusCodeMessages::STATUS_200)
            ->assertJsonCount(5, self::RESPONSE_KEY_DATA);
    }

    /**
     * user Questionnaire crerate data
     * @return array
     */
    public function createUserQuestionnairesDataProvider(): array
    {
        $this->createApplication();

        $requestBody = [Questionnaires::QUESTIONS => [self::createRequestBodyOfQuestionAnswer(1, 'test text')]];

        return [
            'create user questionnaire success: new record' => [
                UserQuestionnairesCreateRequest::KEY_ID => 1, // QuestionnairesTableSeeder::SEEDER_DATA_TESTING_LENGTH
                'requestBody' => $requestBody,
                'expect' => StatusCodeMessages::STATUS_201,
            ],
        ];
    }

    /**
     * user Questionnaire create request test.
     * @dataProvider createUserQuestionnairesDataProvider
     * @return void
     */
    public function testCreateUserQuestionnairesSuccess(int $questionnaireId, array $requestBody, int $expect): void
    {
        $response = $this->post(
            route(
                'user.questionnaires.questionnaire.answer.create',
                [UserQuestionnairesCreateRequest::KEY_ID => $questionnaireId]
            ),
            $requestBody,
            headers: self::getHeaders()
        );
        $response->assertStatus($expect);
    }

    /**
     * create user questionnaires request body for answer.
     * @param int $key key
     * @param ?string $text text
     * @param array $choices choices
     * @return array
     */
    private static function createRequestBodyOfQuestionAnswer(
        int $key,
        ?string $text = null,
        array $choices = []
    ): array {
        $question = [
            Questionnaires::QUESTION_KEY_KEY => $key,
            Questionnaires::QUESTION_KEY_TEXT => $text,
            Questionnaires::QUESTION_KEY_CHOICES => $choices,
        ];
        return $question;
    }
}
