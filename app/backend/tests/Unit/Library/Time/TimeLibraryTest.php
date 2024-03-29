<?php

namespace Tests\Unit\Library\Time;

// use PHPUnit\Framework\TestCase;

use App\Library\Time\TimeLibrary;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

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
     * test faker time current date time.
     *
     * @return void
     */
    public function testFakerTimeCurrentDateTime(): void
    {
        // 検証値
        $timeStamp = 1672327993; // 2022-12-30 00:33:13
        TimeLibrary::setFakerTimeStamp($timeStamp);

        $currentDateTime = TimeLibrary::getCurrentDateTime();
        $currentDateTimeExpect = date(TimeLibrary::DEFAULT_DATE_TIME_FORMAT);
        $fakerDateTimeExpect = date(TimeLibrary::DEFAULT_DATE_TIME_FORMAT, $timeStamp);

        // 現在日時とは異なる
        $this->assertNotSame($currentDateTimeExpect, $currentDateTime);
        // 偽装時刻として設定した日時と合致する
        $this->assertEquals($fakerDateTimeExpect, $currentDateTime);

        // 初期化
        TimeLibrary::setFakerTimeStamp(null);
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
     * test get current time stamp.
     *
     * @return void
     */
    public function testTimeStampToDate(): void
    {
        $timeStamp = TimeLibrary::getCurrentDateTimeTimeStamp();

        $this->assertIsString(TimeLibrary::timeStampToDate($timeStamp));
        $this->assertEquals(
            date(TimeLibrary::DEFAULT_DATE_TIME_FORMAT, $timeStamp),
            TimeLibrary::timeStampToDate($timeStamp)
        );
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
     * test add hours.
     *
     * @return void
     */
    public function testAddHours(): void
    {
        $dateTime = TimeLibrary::getCurrentDateTime();
        $hours = 2;

        $this->assertEquals(
            date(TimeLibrary::DATE_TIME_FORMAT_YMDHIS, strtotime("+$hours hour")),
            TimeLibrary::addHours($dateTime, $hours, TimeLibrary::DATE_TIME_FORMAT_YMDHIS)
        );
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
            date(TimeLibrary::DATE_TIME_FORMAT_YMD, strtotime("+$days days")),
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
            date(TimeLibrary::DATE_TIME_FORMAT_YMD, strtotime("+$months month")),
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
            date(TimeLibrary::DATE_TIME_FORMAT_YMD, strtotime("+$years year")),
            TimeLibrary::addYears($dateTime, $years, TimeLibrary::DATE_TIME_FORMAT_YMD)
        );
    }

    /**
     * test sub days.
     *
     * @return void
     */
    public function testSubDays(): void
    {
        $dateTime = TimeLibrary::getCurrentDateTime();
        $days = 2;

        // 確認用
        // echo date(TimeLibrary::DATE_TIME_FORMAT_YMD,strtotime("+${days} days")) . "\n";
        // echo TimeLibrary::addDays($dateTime, $days, TimeLibrary::DATE_TIME_FORMAT_YMD) . "\n";

        $this->assertEquals(
            date(TimeLibrary::DATE_TIME_FORMAT_YMD, strtotime("-$days days")),
            TimeLibrary::subDays($dateTime, $days, TimeLibrary::DATE_TIME_FORMAT_YMD)
        );
    }

    /**
     * test sub months.
     *
     * @return void
     */
    public function testSubMonths(): void
    {
        $dateTime = TimeLibrary::getCurrentDateTime();
        $months = 2;


        // 確認用
        // echo date(TimeLibrary::DATE_TIME_FORMAT_YMD, strtotime("+${months} month")) . "\n";
        // echo TimeLibrary::addMonths($dateTime, $months, TimeLibrary::DATE_TIME_FORMAT_YMD) . "\n";

        $this->assertEquals(
            date(TimeLibrary::DATE_TIME_FORMAT_YMD, strtotime("-$months month")),
            TimeLibrary::subMonths($dateTime, $months, TimeLibrary::DATE_TIME_FORMAT_YMD)
        );
    }

    /**
     * test sub years.
     *
     * @return void
     */
    public function testSubYears(): void
    {
        $dateTime = TimeLibrary::getCurrentDateTime();
        $years = 2;


        // 確認用
        // echo date(TimeLibrary::DATE_TIME_FORMAT_YMD, strtotime("+${years} month")) . "\n";
        // echo TimeLibrary::addYears($dateTime, $years, TimeLibrary::DATE_TIME_FORMAT_YMD) . "\n";

        $this->assertEquals(
            date(TimeLibrary::DATE_TIME_FORMAT_YMD, strtotime("-$years year")),
            TimeLibrary::subYears($dateTime, $years, TimeLibrary::DATE_TIME_FORMAT_YMD)
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
     * test greater than days.
     *
     * @return void
     */
    public function testGreaterThanDateTime(): void
    {
        $dateTime = TimeLibrary::getCurrentDateTime();
        $days = 3;
        $targetDateTime = date(TimeLibrary::DEFAULT_DATE_TIME_FORMAT, strtotime("-$days days"));

        // echo $interval->format('%d days');

        $this->assertEquals(
            (strtotime($dateTime) > strtotime($targetDateTime)),
            TimeLibrary::greater($dateTime, $targetDateTime)
        );
    }

    /**
     * test lesser than days.
     *
     * @return void
     */
    public function testLesserThanDateTime(): void
    {
        $dateTime = TimeLibrary::getCurrentDateTime();
        $days = 3;
        $targetDateTime = date(TimeLibrary::DEFAULT_DATE_TIME_FORMAT, strtotime("+$days days"));

        $this->assertEquals(
            (strtotime($dateTime) < strtotime($targetDateTime)),
            TimeLibrary::lesser($dateTime, $targetDateTime)
        );
    }

    /**
     * test check date data.
     * @return array
     */
    public static function checkDateDataProvider(): array
    {
        return [
            '2023/01/12' => [
                'value' => '2023/01/12',
                'expect' => true,
            ],
            '2023-01-12' => [
                'value' => '2023-01-12',
                'expect' => false,
            ],
            '2023/01/12 00:00:00' => [
                'value' => '2023/01/12 00:00:00',
                'expect' => false,
            ],
        ];
    }

    /**
     * test start day of month data.
     * @return array
     */
    public static function startDayOfMonthDataProvider(): array
    {
        self::createApplicationForStaticDataProvider();
        $testDateTime = '2023/01/12 00:00:00';
        $testTimestamp = strtotime($testDateTime);
        $testExpect = '2023-01-01';

        $testDateTimeFromCurrentDateTime =
            date(TimeLibrary::DEFAULT_DATE_TIME_FORMAT_DATE_ONLY, strtotime('first day of '));
        return [
            "$testDateTime/result format=Y-m-d/" => [
                'value' => $testTimestamp,
                'format' => TimeLibrary::DEFAULT_DATE_TIME_FORMAT_DATE_ONLY,
                'expect' => $testExpect,
            ],
            "$testDateTime/result format=Y-m-d H:i:s" => [
                'value' => $testTimestamp,
                'format' => TimeLibrary::DEFAULT_DATE_TIME_FORMAT,
                'expect' => "$testExpect 00:00:00",
            ],
            "value=null/result format=Y-m-d H:i:s" => [
                'value' => null,
                'format' => TimeLibrary::DEFAULT_DATE_TIME_FORMAT_DATE_ONLY,
                'expect' => $testDateTimeFromCurrentDateTime,
            ],
        ];
    }

    /**
     * test last day of month data.
     * @return array
     */
    public static function lastDayOfMonthDataProvider(): array
    {
        self::createApplicationForStaticDataProvider();
        $testDateTime = '2023/01/12 00:00:00';
        $testTimestamp = strtotime($testDateTime);
        $testExpect = '2023-01-31';

        $testDateTimeFromCurrentDateTime =
            date(TimeLibrary::DEFAULT_DATE_TIME_FORMAT_DATE_ONLY, strtotime('last day of '));
        return [
            "$testDateTime/result format=Y-m-d/" => [
                'value' => $testTimestamp,
                'format' => TimeLibrary::DEFAULT_DATE_TIME_FORMAT_DATE_ONLY,
                'expect' => $testExpect,
            ],
            "$testDateTime/result format=Y-m-d H:i:s" => [
                'value' => $testTimestamp,
                'format' => TimeLibrary::DEFAULT_DATE_TIME_FORMAT,
                'expect' => "$testExpect 00:00:00",
            ],
            "value=null/result format=Y-m-d H:i:s" => [
                'value' => null,
                'format' => TimeLibrary::DEFAULT_DATE_TIME_FORMAT_DATE_ONLY,
                'expect' => $testDateTimeFromCurrentDateTime,
            ],
        ];
    }

    /**
     * test check date separated by hyphen data.
     * @return array
     */
    public static function checkDateDataByHyphenDataProvider(): array
    {
        return [
            '2023-01-12' => [
                'value' => '2023-01-12',
                'expect' => true,
            ],
            '2023/01/12' => [
                'value' => '2023/01/12',
                'expect' => false,
            ],
            '2023-01-12 00:00:00' => [
                'value' => '2023-01-12 00:00:00',
                'expect' => false,
            ],
        ];
    }

    /**
     * test check datetime data.
     * @return array
     */
    public static function checkDateDataTimeDataProvider(): array
    {
        return [
            '2023/01/12' => [
                'value' => '2023/01/12',
                'expect' => false,
            ],
            '2023-01-12' => [
                'value' => '2023-01-12',
                'expect' => false,
            ],
            '2023/01/12 00:00:00' => [
                'value' => '2023/01/12 00:00:00',
                'expect' => true,
            ],
            '2023/01/12 23:59:59' => [
                'value' => '2023/01/12 23:59:59',
                'expect' => true,
            ],
            '2023-01-12 00:00:00' => [
                'value' => '2023-01-12 00:00:00',
                'expect' => false,
            ],
            '2023/01/12 24:00:00' => [
                'value' => '2023/01/12 24:00:00',
                'expect' => false,
            ],
            '2023/01/12 24:60:00' => [
                'value' => '2023/01/12 23:60:00',
                'expect' => false,
            ],
            '2023/01/12 24:00:60' => [
                'value' => '2023/01/12 23:00:60',
                'expect' => false,
            ],
        ];
    }

    /**
     * test check datetime separated by hyphen data.
     * @return array
     */
    public static function checkDateDataTimeByHyphenDataProvider(): array
    {
        return [
            '2023/01/12' => [
                'value' => '2023/01/12',
                'expect' => false,
            ],
            '2023-01-12' => [
                'value' => '2023-01-12',
                'expect' => false,
            ],
            '2023-01-12 00:00:00' => [
                'value' => '2023-01-12 00:00:00',
                'expect' => true,
            ],
            '2023-01-12 23:59:59' => [
                'value' => '2023-01-12 23:59:59',
                'expect' => true,
            ],
            '2023/01/12 00:00:00' => [
                'value' => '2023/01/12 00:00:00',
                'expect' => false,
            ],
            '2023-01-12 24:00:00' => [
                'value' => '2023-01-12 24:00:00',
                'expect' => false,
            ],
            '2023-01-12 24:60:00' => [
                'value' => '2023-01-12 23:60:00',
                'expect' => false,
            ],
            '2023-01-12 24:00:60' => [
                'value' => '2023-01-12 23:00:60',
                'expect' => false,
            ],
        ];
    }

    /**
     * test start day of month
     * @dataProvider startDayOfMonthDataProvider
     * @param ?int $value
     * @param string $format
     * @param string $expect
     * @return void
     */
    public function testStartDayOfMonth(?int $value, string $format, string $expect): void
    {
        $this->assertEquals(
            $expect,
            TimeLibrary::startDayOfMonth($value, $format)
        );
    }

    /**
     * test last day of month
     * @dataProvider lastDayOfMonthDataProvider
     * @param ?int $value
     * @param string $format
     * @param string $expect
     * @return void
     */
    public function testLastDayOfMonth(?int $value, string $format, string $expect): void
    {
        $this->assertEquals(
            $expect,
            TimeLibrary::lastDayOfMonth($value, $format)
        );
    }

    /**
     * test check date for hyphen
     * @dataProvider checkDateDataProvider
     * @param string $date
     * @param bool $expect
     * @return void
     */
    public function testCheckDete(string $date, bool $expect): void
    {
        $this->assertEquals(
            $expect,
            TimeLibrary::checkDateFormat($date)
        );
    }

    /**
     * test check date separated by hyphen.
     * @dataProvider checkDateDataByHyphenDataProvider
     * @param string $date
     * @param bool $expect
     * @return void
     */
    public function testCheckDeteByHyphen(string $date, bool $expect): void
    {
        $this->assertEquals(
            $expect,
            TimeLibrary::checkDateFormatByHyphen($date)
        );
    }

    /**
     * test check datetime
     * @dataProvider checkDateDataTimeDataProvider
     * @param string $dateTime
     * @param bool $expect
     * @return void
     */
    public function testCheckTimeDete(string $dateTime, bool $expect): void
    {
        $this->assertEquals(
            $expect,
            TimeLibrary::checkDateTimeFormat($dateTime)
        );
    }

    /**
     * test check datetime for hyphen
     * @dataProvider checkDateDataTimeByHyphenDataProvider
     * @param string $dateTime
     * @param bool $expect
     * @return void
     */
    public function testCheckTimeByHyphenDete(string $dateTime, bool $expect): void
    {
        $this->assertEquals(
            $expect,
            TimeLibrary::checkDateTimeFormatByHyphen($dateTime)
        );
    }
}
