<?php

namespace App\Library\Random;

use Exception;
use App\Exceptions\ExceptionStatusCodeMessages;

class RandomStringLibrary
{
    private const RANDOM_MIN_VALUE_97 = 97; // aの文字
    private const RANDOM_MAX_VALUE_122 = 122; // zの文字

    /**
     * ランダム文字列の作成(a~zまでのアルファベット小文字)
     *
     * @return string random string value
     */
    public static function getRandomStringValue(): string
    {
        $str = chr(mt_rand(self::RANDOM_MIN_VALUE_97, self::RANDOM_MAX_VALUE_122));
        for($i = 0; $i < 10; $i++){
            // 数値を指定することで1バイトの文字を生成する(ASCIIコードによる文字の変換)
            $str .= chr(mt_rand(self::RANDOM_MIN_VALUE_97, self::RANDOM_MAX_VALUE_122));
        }
        return $str;
    }
}
