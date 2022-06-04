<?php

namespace App\Library;

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
     * get current date time.
     *
     * @param string $dateTime 日時
     * @return array 曜日
     */
    public static function getDays(string $dateTime): array
    {
        return (new Carbon($dateTime))->getDays();
    }
}
