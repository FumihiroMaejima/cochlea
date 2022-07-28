<?php

namespace App\Library\Time;

use Illuminate\Http\Request;
use App\Trait\CheckHeaderTrait;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Carbon;

class TimeLibrary
{
    // デフォルトのフォーマット
    public const DEFAULT_DATE_TIME_FORMAT = 'Y-m-d H:i:s'; // ex: 2022-01-01 00:00:00
    public const DEFAULT_DATE_TIME_FORMAT_SLASH = 'Y/m/d H:i:s'; // ex: 2022/01/01 00:00:00

    public const DATE_TIME_FORMAT_YMD = 'Ymd'; // ex: 20220101
    public const DATE_TIME_FORMAT_HIS = 'His'; // ex: 000000
    public const DATE_TIME_FORMAT_YMDHIS = 'YmdHis'; // ex: 20220101125959

    /**
     * get current date time.
     *
     * @param string $format datetime format
     * @return string
     */
    public static function getCurrentDateTime(string $format = self::DEFAULT_DATE_TIME_FORMAT): string
    {
        /* $carbon = new Carbon();
        $test = $carbon->now()->format('Y-m-d H:i:s'); */
        // $dateTime = Carbon::now()->format('Y-m-d H:i:s');

        // return Carbon::now()->format(self::DEFAULT_DATE_TIME_FORMAT);
        return Carbon::now()->timezone(Config::get('app.timezone'))->format($format);
    }

    /**
     * get timestamp of current date time.
     *
     * @return int|float|string timestamp
     */
    public static function getCurrentDateTimeTimeStamp(): int|float|string
    {
        return Carbon::now()->timezone(Config::get('app.timezone'))->timestamp;
    }

    /**
     * get current date timestamp.
     *
     * @param string $dateTime 日時
     * @return int タイムスタンプ
     */
    public static function strToTimeStamp(string $dateTime): int
    {
        return strtotime($dateTime);
    }

    /**
     * get formatted date time.
     *
     * @param string $dateTime 日時
     * @param string $format datetime format
     * @return array 曜日
     */
    public static function format(string $dateTime, string $format = self::DEFAULT_DATE_TIME_FORMAT_SLASH): string
    {
        return (new Carbon($dateTime))->format($format);
    }

    /**
     * get parameter days.
     *
     * @param string $dateTime 日時
     * @return array 曜日
     */
    public static function getDays(string $dateTime): array
    {
        return (new Carbon($dateTime))->getDays();
    }

    /**
     * add days to dateTime parameter.
     *
     * @param string $dateTime 日時
     * @param int $value 加算日数
     * @param string $format datetime format
     * @return string $dateTimeから$value日後の$dateTime
     */
    public static function addDays(string $dateTime, int $value, string $format = self::DEFAULT_DATE_TIME_FORMAT): string
    {
        return (new Carbon($dateTime))->addDays($value)->format($format);
    }

    /**
     * add mounth to dateTime parameter.
     *
     * @param string $dateTime 日時
     * @param int $value 加算月数
     * @param string $format datetime format
     * @return string $dateTimeから$valueヶ月後の$dateTime
     */
    public static function addMounths(string $dateTime, int $value, string $format = self::DEFAULT_DATE_TIME_FORMAT): string
    {
        return (new Carbon($dateTime))->addMonths($value)->format($format);
    }

    /**
     * add mounth to dateTime parameter.
     *
     * @param string $dateTime 日時
     * @param int $value 加算年数
     * @param string $format datetime format
     * @return string $dateTimeの$value年後のdateTime
     */
    public static function addYears(string $dateTime, int $value, string $format = self::DEFAULT_DATE_TIME_FORMAT): string
    {
        return (new Carbon($dateTime))->addYears($value)->format($format);
    }
}
