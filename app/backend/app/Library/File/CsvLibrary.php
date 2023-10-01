<?php

namespace App\Library\File;

use Illuminate\Support\Facades\Storage;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use Exception;

class CsvLibrary
{
    public const DIRECTORY = 'csv/';

    /**
     * get csv file contents
     *
     * @param string $path fileName file name & extention.
     * @return array
     * @throws Exception
     */
    public static function getFileStoream(string $fileName = 'default/test1.csv'): array
    {
        // storageまでのパスを追加してルートからのパスの整形
        $path = storage_path(self::DIRECTORY . $fileName);
        // $path = self::DIRECTORY . $fileName;
        // storage/app直下に無い為file_get_contents()で取得
        // $file = file_get_contents(storage_path($path));

        // ファイルパスを指定し、resourceIdを取得する
        $file = fopen($path, 'r');
        echo 'file: ' . $file . "\n";

        $headers = [];
        $fileData = [];

        // ファイルの内容を一行ずつ配列に代入
        $tmp = [];
        if ($file) {
            while ($line = fgets($file)) {
                echo 'line: ' . $line;
                $tmp[] = trim($line);
            }
        }

        // 配列の各要素をさらに分解
        foreach ($tmp as $key => $value) {
            if ($key === 0) {
                $headers = $value;
            } else {
                // カンマを境目に配列データとする
                $fileData[] = explode(',', $value);
            }
        }

        // resource idを指定してファイルを閉じる
        fclose($file);

        return $fileData;
    }

    /**
     * filter lesser than threshold.
     *
     * @param array $items
     * @param int $threshold
     * @return array
     * @throws Exception
     */
    public static function filteringIsLower(array $items, int $threshold = 30): array
    {
        $response = [];
        foreach($items as $item) {
            if ($item <= $threshold) {
                $response = $item;
            }
        }
        return $response;
    }


    /**
     * get average of item column.
     *
     * @param array $items
     * string $columnName column name
     * @return int
     * @throws Exception
     */
    public static function getAverage(array $items, string $columnName): int
    {
        $count = count($items);
        $values = array_column($items, $columnName);
        $sum = 0;
        foreach($values as $value) {
            $sum = $value;
        }

        return (int)floor($sum / $count);
    }
}
