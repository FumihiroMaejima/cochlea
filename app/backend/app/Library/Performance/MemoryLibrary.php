<?php

namespace App\Library\Performance;

class MemoryLibrary
{
    // memory_get_usage(TRUE) = システム上から割り当てられている容量=あらかじめ確保されているメモリ容量=php.iniやini_setで設定した「memory_limit」のメモリ上限
    // memory_get_usage(FALSE) = スクリプトが実際に使用しているメモリ容量

    public const MEMORY_UNIT = ['B','KB','MB','GB','TB','PB'];
    public const BASE_BIT = 1024;

    /**
     * convert memory usage unit.
     *
     * @param int $size
     * @return string
     */
    public static function convert(int $size): string
    {
        // この場合のlogは自然対数のこと。log(base)numを返す
        return round($size/pow(self::BASE_BIT,($i=floor(log($size,self::BASE_BIT)))),2) . ' ' . self::MEMORY_UNIT[$i];
    }
}
