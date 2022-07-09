<?php

namespace App\Library\String;

use Illuminate\Http\Request;
use App\Trait\CheckHeaderTrait;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Carbon;

class UnidLibrary
{
    // version4のパターン
    public const PATTERN_V4 = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx';

    public const RANDOM_INT_MIN_0 = 0;
    public const RANDOM_INT_MIN_8 = 8;
    public const RANDOM_INT_MAX_11 = 11;
    public const RANDOM_INT_MAX_15 = 15;

    /**
     * generate uui version4
     *
     * @return string uuid
     */
    public static function uuidVersion4(): string
    {
        $chars = str_split(self::PATTERN_V4);

        foreach ($chars as $i => $char) {
            if ($char === 'x') {
                $chars[$i] = dechex(random_int(self::RANDOM_INT_MIN_0, self::RANDOM_INT_MAX_15));
            } elseif ($char === 'y') {
                $chars[$i] = dechex(random_int(self::RANDOM_INT_MIN_8, self::RANDOM_INT_MAX_11));
            }
        }

        return implode('', $chars);
    }
}
