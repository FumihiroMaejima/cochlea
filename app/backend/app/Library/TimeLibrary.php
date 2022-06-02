<?php

namespace App\Library;

use Illuminate\Http\Request;
use App\Trait\CheckHeaderTrait;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Carbon;

class TimeLibrary
{
    // デフォルトのフォーマット
    private const DEFAULT_DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * get current date time.
     *
     * @return string
     */
    public static function getCurrentDateTime(): string
    {
        /* $carbon = new Carbon();
        $test = $carbon->now()->format('Y-m-d H:i:s'); */
        // $dateTime = Carbon::now()->format('Y-m-d H:i:s');

        return Carbon::now()->format(self::DEFAULT_DATE_TIME_FORMAT);
    }
}
