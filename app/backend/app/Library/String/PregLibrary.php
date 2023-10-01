<?php

namespace App\Library\String;

class PregLibrary
{
    /**
     * filtering string value by number & return int value & chage typet to int
     * @param string $value
     * @param int
     *
     * @return string uuid
     */
    public static function filteringByNumber(string $value): int
    {
        // 0～9以外は空白に変換して文字列だけを取得する。
        return (int)preg_replace('/[^0-9]/', '', $value);
    }
}
