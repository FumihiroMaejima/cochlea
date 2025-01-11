<?php

namespace Tests\Unit\Library\User;

use App\Library\Debug\WebConsole;
use App\Library\User\UserLibrary;
use App\Library\Hash\HashLibrary;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\TestCase;
//use Tests\TestCase;
use Mockery;

class MockeryTest extends TestCase
{
    /**
     * setUpは各テストメソッドが実行される前に実行する
     * 親クラスのsetUpを必ず実行する
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        // Mockery設定のリセット
        // これとは個別に各アノテーションでテストごとに別プロセス実行させる必要がある。
        Mockery::close();
        parent::tearDown();
    }

    /**
     * validate user password data
     * @return array
     */
    public static function validateUserPasswordDataProvider(): array
    {
        // TODO php8.3以降、Laravelのテストクラスでstaticメソッドのテストが上手く動かなくなっている。
        // 会費の為`PHPUnit\Framework\TestCase`を継承している。
        // この為、Laravel関係のクラスを使えない。

        // self::createApplicationForStaticDataProvider();
        return [
            'check user password' => [
                'password' => 'testPassword',
                'user' => [
                    User::ID => 1,
                    User::SALT => 'testSalt',
                    // User::PASSWORD => bcrypt('testPassword'.'testSalt'.'testPepper123'),
                    User::PASSWORD => 'test',
                ],
                'expect' => true,
            ],
        ];
    }

    /**
     * test validate user password.
     *
     * @dataProvider validateUserPasswordDataProvider
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @param string $password
     * @param array $user
     * @param bool $expect
     * @return void
     */
    public function testValidateUserPassword(
        string $password,
        array $user,
        bool $expect
    ): void {
        $testPepper = 'testPepper123';

        // テスト用のpepperを返す様にモックを設定

        $mock = (new Mockery())->mock('overload:'.HashLibrary::class);
        $mock->shouldReceive('getPepper')->once()->andReturn($testPepper);

        // Hash::classのcheckメソッドをモック化
        $mock2 = (new Mockery())->mock('overload:'.Hash::class);
        $mock2->shouldReceive('check')->once()->andReturn(true);

        $result = UserLibrary::validateUserPassword($password, $user);
        // Mockery::close();

        $this->assertEquals($result, $expect);
    }

    /**
     * test static method mock.
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @return void
     */
    public function testStaticMethodMock(): void
    {
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
     * staticメソッドのモックでも上記のアノテーションをつけないと他のテストクラスで影響が出てくる
     * @return void
     */
    public function testOverLoadMock(): void
    {
        $value = [123];
        // UserLibraryクラス内で使っているUserクラスのメソッドのモックの作成
        // $mock = Mockery::mock('overload:'.\App\Models\User::class);
        $mock = (new Mockery())->mock('overload:'.User::class);
        $mock->shouldReceive('getRecordByUserId')->once()->andReturn($value);

        $result = UserLibrary::lockUser(1);

        $this->assertEquals($result, $value);
    }
}
