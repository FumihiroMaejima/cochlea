<?php

namespace App\Library\File;

use Illuminate\Support\Facades\Storage;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Library\String\PregLibrary;
use Exception;
use SplFileObject;

class TsvLibrary
{
    public const DIRECTORY = 'tsv/';

    /**
     * get csv file contents by SplFileObject
     *
     * @param string $path fileName file name & extention.
     * @return array
     * @throws Exception
     */
    public static function getFileStoream(string $fileName = 'default/test1.tsv'): array
    {
        $time = microtime(true);
        $memory = memory_get_usage();
        // storageまでのパスを追加してルートからのパスの整形
        $path = storage_path(self::DIRECTORY . $fileName);

        $file = new SplFileObject($path);
        foreach ($file as $line) {
            // $fileRecords[] = explode(' ', $line);
            $fileRecords[] = $line;
        }

        $endTime = microtime(true) - $time;
        $usageMemory = memory_get_usage() - $memory;

        printf('time: %s' . "\n", $endTime);
        printf('usageMemory: %s' . "\n", $usageMemory);

        return $fileRecords;
    }

    /**
     * filter lesser than threshold.
     *
     * @param array $items
     * @param int|string $columnName column name or index
     * @param int $threshold
     * @return array
     * @throws Exception
     */
    public static function filteringIsLower(array $items, int|string $columnName, int $threshold = 30): array
    {
        $response = [];
        foreach($items as $item) {
            $value = PregLibrary::filteringByNumber($item[$columnName]);
            if (!is_null($value) && $value <= $threshold) {
                $response[] = $item;
            }
        }
        return $response;
    }


    /**
     * get average of item column.
     *
     * @param array $items
     * @param int|string $columnName column name or index
     * @return float
     * @throws Exception
     */
    public static function getAverage(array $items, int|string $columnName): float
    {
        $count = count($items);
        $values = [];
        foreach($items as $item) {
            // 数字以外の文字は空文字列に差し替えてから格納
            $values[] = PregLibrary::filteringByNumber($item[$columnName]) ?? 0;
        }
        $sum = array_sum($values);

        // 少数第2位まで表示。第3位は四捨五入
        return round($sum / $count, 2);
    }
}
