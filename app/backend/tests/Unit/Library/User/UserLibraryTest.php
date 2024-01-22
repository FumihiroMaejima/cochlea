<?php

namespace Tests\Unit\Library\User;

use App\Library\Debug\WebConsole;
use App\Library\User\UserLibrary;
use App\Library\Hash\HashLibrary;
use App\Models\User;
use Tests\TestCase;
use Mockery;

class UserLibraryTest extends TestCase
{
    /**
     * setUpは各テストメソッドが実行される前に実行する
     * 親クラスのsetUpを必ず実行する
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * validate user password data
     * @return array
     */
    public static function validateUserPasswordDataProvider(): array
    {
        self::createApplicationForStaticDataProvider();
        return [
            'check user password' => [
                'command' => 'ls',
                'user' => [
                    User::ID => 1,
                    User::SALT => 'testSalt',
                    User::PASSWORD => 'testPassword',
                ],
                'expect' => false,
            ],
        ];
    }

    /**
     * test validate user password.
     *
     * @dataProvider validateUserPasswordDataProvider
     *
     * @param string $value
     * @param array $user
     * @param bool $expect
     * @return void
     */
    /* public function testValidateUserPassword(
        string $value,
        array $user,
        bool $expect
    ): void {
        $userMock = Mockery::mock('overload:'.\App\Models\User::class)->makePartial();
        $userMock->shouldReceive('getRecordByUserId')->once()->andReturn([123]);
        $result = UserLibrary::lockUser(1);

        $this->assertEquals($result, $expect);
    } */

    /**
     * test static method mock.
     *
     * @return void
     */
    public function testStaticMethodMock(): void {
        $value = [456];
        // UserLibraryクラスのstaticメソッド、lockUser()をモック化させる
        $mock = Mockery::mock('alias:' . UserLibrary::class);
        $mock->shouldReceive('lockUser')->once()->andReturn($value);

        $result = UserLibrary::lockUser(1);

        $this->assertEquals($result, $value);
    }

    /**
     * test for overload class mock.
     * *特定のクラスのoverloadする場合は下記のアノテーションをつける
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * runInSeparateProcess:該当のテストを個別のプロセスで実行するようする
     * preserveGlobalState:テストを別プロセスで実行するときに、親プロセスのグローバルな状態を保存するのを無効化する
     * @return void
     */
    public function testOverLoadMock(): void {
        $value = [123];
        // UserLibraryクラス内で使っているUserクラスのメソッドのモックの作成
        $mock = Mockery::mock('overload:'.\App\Models\User::class);
        $mock->shouldReceive('getRecordByUserId')->once()->andReturn($value);

        $result = UserLibrary::lockUser(1);

        $this->assertEquals($result, $value);
    }
}
