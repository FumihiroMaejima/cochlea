<?php

namespace Tests\Unit\Service;

// use PHPUnit\Framework\TestCase;

use App\Library\TimeLibrary;
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
     * get current dateTime test.
     *
     * @return void
     */
    public function testGetCurrentDateTime(): void
    {
        // 「20xx-01-01」の様な形式データ
        $expect = mb_substr(date(TimeLibrary::DEFAULT_DATE_TIME_FORMAT), 0, 9);

        $this->assertStringStartsWith($expect, TimeLibrary::getCurrentDateTime());
    }

    /**
     * member crerate data
     * @return array
     */
    /* public function memberCreateDataProvider(): array
    {
        $this->createApplication();

        return [
            'create member data' => Config::get('myapp.test.member.create.success')
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
