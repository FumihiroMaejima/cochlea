<?php

namespace Tests\Unit\Service\Time;

// use PHPUnit\Framework\TestCase;

use App\Library\Time\TimeLibrary;
use Tests\TestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\WithFaker;
use DateTime;

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
     * test current datetime format.
     *
     * @return void
     */
    public function testCheckGettingCurrentDateTimeFormat(): void
    {
        // 「20xx-01-01」の様な形式データ
        $expect = mb_substr(date(TimeLibrary::DEFAULT_DATE_TIME_FORMAT), 0, 10);

        $this->assertEquals($expect, TimeLibrary::getCurrentDateTime(TimeLibrary::DEFAULT_DATE_TIME_FORMAT_DATE_ONLY));
    }

    /**
     * test get formatted date.
     *
     * @return void
     */
    public function testFormatedDate(): void
    {
        $dateTime = TimeLibrary::getCurrentDateTime();

        $this->assertEquals(date(TimeLibrary::DATE_TIME_FORMAT_YMD), TimeLibrary::format($dateTime, TimeLibrary::DATE_TIME_FORMAT_YMD));
        $this->assertEquals(date(TimeLibrary::DEFAULT_DATE_TIME_FORMAT_DATE_ONLY), TimeLibrary::format($dateTime, TimeLibrary::DEFAULT_DATE_TIME_FORMAT_DATE_ONLY));
    }

    /**
     * test add days.
     *
     * @return void
     */
    public function testAddDays(): void
    {
        $dateTime = TimeLibrary::getCurrentDateTime();
        $days = 2;

        // 確認用
        // echo date(TimeLibrary::DATE_TIME_FORMAT_YMD,strtotime("+${days} days")) . "\n";
        // echo TimeLibrary::addDays($dateTime, $days, TimeLibrary::DATE_TIME_FORMAT_YMD) . "\n";

        $this->assertEquals(
            date(TimeLibrary::DATE_TIME_FORMAT_YMD, strtotime("+${days} days")),
            TimeLibrary::addDays($dateTime, $days, TimeLibrary::DATE_TIME_FORMAT_YMD)
        );
    }

    /**
     * test add months.
     *
     * @return void
     */
    public function testAddMonths(): void
    {
        $dateTime = TimeLibrary::getCurrentDateTime();
        $months = 2;


        // 確認用
        // echo date(TimeLibrary::DATE_TIME_FORMAT_YMD, strtotime("+${months} month")) . "\n";
        // echo TimeLibrary::addMonths($dateTime, $months, TimeLibrary::DATE_TIME_FORMAT_YMD) . "\n";

        $this->assertEquals(
            date(TimeLibrary::DATE_TIME_FORMAT_YMD, strtotime("+${months} month")),
            TimeLibrary::addMonths($dateTime, $months, TimeLibrary::DATE_TIME_FORMAT_YMD)
        );
    }

    /**
     * test add years.
     *
     * @return void
     */
    public function testAddYears(): void
    {
        $dateTime = TimeLibrary::getCurrentDateTime();
        $years = 2;


        // 確認用
        // echo date(TimeLibrary::DATE_TIME_FORMAT_YMD, strtotime("+${years} month")) . "\n";
        // echo TimeLibrary::addYears($dateTime, $years, TimeLibrary::DATE_TIME_FORMAT_YMD) . "\n";

        $this->assertEquals(
            date(TimeLibrary::DATE_TIME_FORMAT_YMD, strtotime("+${years} year")),
            TimeLibrary::addYears($dateTime, $years, TimeLibrary::DATE_TIME_FORMAT_YMD)
        );
    }

    /**
     * test diff days.
     *
     * @return void
     */
    public function testDiffDays(): void
    {
        $dateTime = TimeLibrary::getCurrentDateTime();
        $days = 3;
        $targetDateTime = TimeLibrary::addDays($dateTime, $days, TimeLibrary::DATE_TIME_FORMAT_YMD);

        $origin = date_create_immutable($dateTime);
        $target = date_create_immutable($targetDateTime);
        $interval = date_diff($origin, $target);
        // echo $interval->format('%d days');

        $this->assertEquals(
            $interval->format('%d'),
            TimeLibrary::diffDays($dateTime, $targetDateTime)
        );
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
