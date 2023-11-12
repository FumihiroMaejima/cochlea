<?php

declare(strict_types=1);

namespace Tests\Unit\Library\CLI;

// use PHPUnit\Framework\TestCase;

use Tests\TestCase;
use Exception;

class MultiLineParamSampleLibraryTest extends TestCase
{
    private const TEST_DATA_KEY_COUNT = 'count';
    private const TEST_DATA_KEY_ITEMS = 'items';

    private const EOF_STAT_STRING = "<<EOF";
    private const EOF_END_STRING = "EOF";

    private const FILE_PATH = 'APP/Library/CLI/MultiLineParamSampleLibrary.php';

    private string $testFilePath = './' . self::FILE_PATH;

    /**
     * test paramteter data
     * @return array
     */
    public static function sampleDataProvider(): array
    {
        return [
            'count: 3, param: 2, 1, 2' => [
                self::TEST_DATA_KEY_COUNT  => 3,
                self::TEST_DATA_KEY_ITEMS  => [2, 1, 2],
            ],
            'count: 5, param: 20, 12, 25, 29, 31' => [
                self::TEST_DATA_KEY_COUNT  => 5,
                self::TEST_DATA_KEY_ITEMS  => [20, 12, 25, 29, 31],
            ],
            'count: 2, param: 10, -30' => [
                self::TEST_DATA_KEY_COUNT  => 2,
                self::TEST_DATA_KEY_ITEMS  => [10, -30],
            ],
        ];
    }

    /**
     * sample test.
     *
     * @dataProvider sampleDataProvider
     * @return void
     */
    public function testSample(int $count, array $items): void
    {
        $result = null;

        $totalValue = 0;
        $tmpValues = '';

        // ヒアドキュメントのテキストと合計値の作成
        foreach ($items as $key => $item) {
            if ($key === 0) {
                $tmpValues = "\n" . $count . "\n" . $item . "\n";
                $totalValue = $item;
            } else {
                $tmpValues .= $item . "\n";
                $totalValue += $item;
            }
        }
        $values = self::EOF_STAT_STRING . $tmpValues . self::EOF_END_STRING;

        $command ="php {$this->testFilePath} {$values}";

        // proc_open()で実行する場合
        // パイプで入出力を受け取る
        $descriptor = [
            0 => ['pipe','r'],
            1 => ['pipe','w'],
        ];

        $process = proc_open($command, $descriptor, $pipes);

        if (is_resource($process)) {
            // メインプロセスの標準入力
            fclose($pipes[0]);

            // メインプロセスの標準出力
            $result = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
        }
        proc_close($process);

        // echo "result: ---------------------\n";
        // echo var_dump($result);

        $this->assertIsString($result);
        $this->assertTrue(str_contains($result, (string)$totalValue));


        // exec()で実行する場合
        // exec("{$command}", $result);
        // $this->assertIsArray($result);
        // $this->assertTrue(str_contains($result[0], $totalValue));
    }
}
