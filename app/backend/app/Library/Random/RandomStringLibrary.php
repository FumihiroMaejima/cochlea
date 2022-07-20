<?php

namespace App\Library\Random;

use Exception;
use App\Exceptions\ExceptionStatusCodeMessages;

class RandomStringLibrary
{
    private const RANDOM_MIN_VALUE_97 = 97; // aの文字
    private const RANDOM_MAX_VALUE_122 = 122; // zの文字

    private const RANDOM_STRINGS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    private const RANDOM_SYMBOL_STRINGS = '!@#$%^&*()-_=+[{]}\|;:,<.>/?\'\"';
    private const DEFAULT_RANDOM_STRING_LENGTH = 12;

    // hash algorithm
    private const HASH_ALGORITHM_256 = 'sha256';

    /**
     * ランダム文字列の作成(a~zまでのアルファベット小文字)
     *
     * @return string random string value
     */
    public static function getRandomStringValue(): string
    {
        $str = chr(mt_rand(self::RANDOM_MIN_VALUE_97, self::RANDOM_MAX_VALUE_122));
        for ($i = 0; $i < 10; $i++) {
            // 数値を指定することで1バイトの文字を生成する(ASCIIコードによる文字の変換)
            $str .= chr(mt_rand(self::RANDOM_MIN_VALUE_97, self::RANDOM_MAX_VALUE_122));
        }
        return $str;
    }

    /**
     * ランダム文字列の作成(パラメーターで長さ指定)
     *
     * @param int $length random string length
     * @return string random string value
     */
    public static function getRandomShuffleString(int $length = self::DEFAULT_RANDOM_STRING_LENGTH): string
    {
        // 同じ文字は2回出ない
        return substr(str_shuffle(self::RANDOM_STRINGS), 0, $length);
    }

    /**
     * mt_rand()でランダム文字列の作成(パラメーターで長さ指定)
     *
     * @param int $length random string length
     * @return string random string value
     */
    public static function getByMtRandString(int $length = self::DEFAULT_RANDOM_STRING_LENGTH): string
    {
        // 同じ文字が複数出現する可能性あり

        // 数値の基数を任意に変換する
        return base_convert(mt_rand(pow(36, $length - 1), pow(36, $length) - 1), 10, 36);
    }

    /**
     * md5()とuniqid()でランダム文字列の作成(パラメーターで長さ指定)
     *
     * @param int $length random string length
     * @return string random string value
     */
    public static function getByMd5RandomString(int $length = self::DEFAULT_RANDOM_STRING_LENGTH): string
    {
        return substr(base_convert(md5(uniqid()), 16, 36), 0, $length);
    }

    /**
     * hash('sha256')でランダム文字列の作成(パラメーターで長さ指定)
     *
     * @param int $length random string length
     * @return string random string value
     */
    public static function getByHashRandomString(int $length = self::DEFAULT_RANDOM_STRING_LENGTH): string
    {
        return substr(base_convert(hash(self::HASH_ALGORITHM_256, uniqid()), 16, 36), 0, $length);
    }
}
