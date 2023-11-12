<?php

namespace Tests\Unit\Library\CLI;

// use PHPUnit\Framework\TestCase;

use App\Library\CLI\SampleLibrary;
use Tests\TestCase;
use Exception;

class SampleLibraryTest extends TestCase
{
    private const RESULT_DATA_INDEX_ZERO = 0;
    private const RESULT_DATA_INDEX_FIRST = 1;
    private const RESULT_DATA_INDEX_SECOND = 2;

    private const FILE_PATH = 'APP/Library/CLI/SampleLibrary.php';

    private string $testFilePath = './' . self::FILE_PATH;

    /**
     * test paramteter data
     * @return array
     */
    public static function sampleDataProvider(): array
    {
        return [
            '10 20 30' => [
                'first'  => 10,
                'second' => 20,
                'third'  => 30
            ],
            '0 -1 -10' => [
                'first'  => 0,
                'second' => -1,
                'third'  => -10
            ],
            '0 0 0' => [
                'first'  => 0,
                'second' => 0,
                'third'  => 0
            ],
        ];
    }

    /**
     * a sample test.
     *
     * @dataProvider sampleDataProvider
     * @return void
     */
    public function testSamole(int $first, int $second, int $third): void
    {
        $result = null;

        exec("echo {$first} {$second} {$third} | php {$this->testFilePath}", $result);
        // echo var_dump($result);

        $this->assertIsArray($result);
        $this->assertIsString($result[self::RESULT_DATA_INDEX_ZERO]);
        $this->assertTrue(str_contains($result[self::RESULT_DATA_INDEX_ZERO], $first));
        $this->assertTrue(str_contains($result[self::RESULT_DATA_INDEX_FIRST], $second));
        $this->assertTrue(str_contains($result[self::RESULT_DATA_INDEX_SECOND], $third));
    }
}
