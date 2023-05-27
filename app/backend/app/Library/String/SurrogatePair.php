<?php

namespace App\Library\String;

class SurrogatePair
{
    // サロゲート:UnicodeでU+D800からU+DFFFの範囲のコードポイント。単独では文字を表さずサロゲートペアとして使用
    // サロゲートペア:それぞれ 2バイトのhigh surrogateとlow surrogateが4バイトの組をなして1文字を表わしたもの=1文字が4バイトの文字(絵文字など)

    // mb_convert_encoding():ある文字エンコーディングの文字列を別の文字エンコーディングに変換する
    // echo strlen('絵文字'); // 4
    // echo mb_strlen('絵文字'); // 1
    // echo mb_convert_encoding('絵文字', 'UTF-16'); // b"Ø=Þ\x07"など
    // echo strlen(mb_convert_encoding('絵文字', 'UTF-16')); // 4

    public const UTF_8 = 'UTF-8';
    public const UTF_16 = 'UTF-16';
    public const UTF_32 = 'UTF-32';

    /**
     * check is not
     *
     * @param string $value
     * @return bool
     */
    public static function isNotSurrogatePair(string $value): bool
    {
        // 文字数とbyte数を比較
        return mb_strlen($value) === mb_strlen(mb_convert_encoding($value, self::UTF_16)) / 2;
    }

    // 絵文字を入力してもサロゲートペアにならないケース
    // echo strlen('\U+270A'); // 7
    // echo mb_strlen('\U+270A'); // 7
    // echo mb_convert_encoding('\U+270A', 'UTF-16'); // "\0\\0U\0+\02\07\00\0A"
    // echo strlen(mb_convert_encoding('\U+270A', 'UTF-16')); // 14

    /**
     * get unicode of emoji
     *
     * @param string $value
     * @param bool $isHex is hex. default decimal
     * @return string
     */
    public static function getUnicodeFromEmoji(string $value, bool $isHex = false): string
    {
        // UTF-8からUTF-32へ変換
        $emojiBinary = mb_convert_encoding($value, self::UTF_32, self::UTF_8);
        // 16進数
        $hex = bin2hex($emojiBinary);

        if ($isHex) {
            return $hex;
        } else {
            return (string)hexdec($hex);
        }
    }
}
