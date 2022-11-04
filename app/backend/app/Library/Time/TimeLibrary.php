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
    public const DEFAULT_DATE_TIME_FORMAT_DATE_ONLY = 'Y-m-d'; // ex: 2022-01-01

    public const DATE_TIME_FORMAT_YMD = 'Ymd'; // ex: 20220101
    public const DATE_TIME_FORMAT_HIS = 'His'; // ex: 125959
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
    public static function addMonths(string $dateTime, int $value, string $format = self::DEFAULT_DATE_TIME_FORMAT): string
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

    /**
     * sub days to dateTime parameter.
     *
     * @param string $dateTime 日時
     * @param int $value 減算日数
     * @param string $format datetime format
     * @return string $dateTimeから$value日前の$dateTime
     */
    public static function subDays(string $dateTime, int $value, string $format = self::DEFAULT_DATE_TIME_FORMAT): string
    {
        return (new Carbon($dateTime))->subDays($value)->format($format);
    }

    /**
     * sub mounth to dateTime parameter.
     *
     * @param string $dateTime 日時
     * @param int $value 減算月数
     * @param string $format datetime format
     * @return string $dateTimeから$valueヶ月前の$dateTime
     */
    public static function subMonths(string $dateTime, int $value, string $format = self::DEFAULT_DATE_TIME_FORMAT): string
    {
        return (new Carbon($dateTime))->subMonths($value)->format($format);
    }

    /**
     * sub mounth to dateTime parameter.
     *
     * @param string $dateTime 日時
     * @param int $value 減算年数
     * @param string $format datetime format
     * @return string $dateTimeの$value年前のdateTime
     */
    public static function subYears(string $dateTime, int $value, string $format = self::DEFAULT_DATE_TIME_FORMAT): string
    {
        return (new Carbon($dateTime))->subYears($value)->format($format);
    }

    /**
     * add mounth to dateTime parameter.
     *
     * @param string $dateTime 日時
     * @param string $targetDateTime 比較対象の日付
     * @param string $format datetime format
     * @return int 日数
     */
    public static function diffDays(string $dateTime, string $targetDateTime): int
    {
        return (new Carbon($dateTime))->diffInDays($targetDateTime);
    }

    /**
     * whichever dateTime is greater than target.
     *
     * @param string $dateTime 日時
     * @param string $targetDateTime 比較対象の日付
     * @param string $format datetime format
     * @return bool 日数
     */
    public static function greater(string $dateTime, string $targetDateTime): bool
    {
        return (new Carbon($dateTime))->greaterThan($targetDateTime);
    }

    /**
     * whichever dateTime is less than target.
     *
     * @param string $dateTime 日時
     * @param string $targetDateTime 比較対象の日付
     * @param string $format datetime format
     * @return bool 日数
     */
    public static function lesser(string $dateTime, string $targetDateTime): bool
    {
        return (new Carbon($dateTime))->lessThan($targetDateTime);
    }
}
