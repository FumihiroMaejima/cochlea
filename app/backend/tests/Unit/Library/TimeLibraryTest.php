<?php

namespace Tests\Unit\Service;

// use PHPUnit\Framework\TestCase;

use App\Library\Time\TimeLibrary;
use Tests\TestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\WithFaker;

// use Illuminate\Foundation\Testing\DatabaseMigrations;
// use Illuminate\Foundation\Testing\RefreshDatabase;

class TimeLibraryTest extends TestCase
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
     * test get current dateTime test.
     *
     * @return void
     */
    public function testGetCurrentDateTime(): void
    {
        // 「20xx-01-01」の様な形式データ
        $expect = mb_substr(date(TimeLibrary::DEFAULT_DATE_TIME_FORMAT), 0, 10);

        $this->assertStringStartsWith($expect, TimeLibrary::getCurrentDateTime());
    }

    /**
     * test get current time stamp.
     *
     * @return void
     */
    public function testStrToTimeStamp(): void
    {
        $dateTime = TimeLibrary::getCurrentDateTime();

        $this->assertIsInt(TimeLibrary::strToTimeStamp($dateTime));
        $this->assertEquals(strtotime($dateTime), TimeLibrary::strToTimeStamp($dateTime));
    }

    /**
     * test get formatted date time.
     *
     * @return void
     */
    public function testFormated(): void
    {
        // 「20xx-01-01」の様な形式データ
        $expect = mb_substr(date(TimeLibrary::DEFAULT_DATE_TIME_FORMAT), 0, 10);

        $this->assertEquals($expect, TimeLibrary::getCurrentDateTime(TimeLibrary::DEFAULT_DATE_TIME_FORMAT_DATE_ONLY));
    }

    /**
     * member crerate data
     * @return array
     */
    /* public function memberCreateDataProvider(): array
    {
        $this->createApplication();

        return [
            'create member data' => Config::get('myappTest.test.member.create.success')
        ];
    } */

    /**
     * members create request test.
     * @dataProvider memberCreateDataProvider
     * @return void
     */
   /*  public function testCreateMemberSuccess(string $name, string $email, int $roleId, string $password, string $password_confirmation): void
    {
        $response = $this->json('POST', route('admin.admins.create'), [
            'name'                  => $name,
            'email'                 => $email,
            'roleId'                => $roleId,
            'password'              => $password,
            'password_confirmation' => $password_confirmation
        ]);
        $response->assertStatus(201);
    } */
}
