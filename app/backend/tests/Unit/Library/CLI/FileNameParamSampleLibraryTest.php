<?php

declare(strict_types=1);

namespace Tests\Unit\Library\CLI;

// use PHPUnit\Framework\TestCase;

use Tests\TestCase;
use Exception;
use PHPUnit\Framework\Attributes\DataProvider;

class FileNameParamSampleLibraryTest extends TestCase
{
    private const TEST_DATA_KEY_COUNT = 'count';
    private const TEST_DATA_KEY_ITEMS = 'items';

    private const EOF_STAT_STRING = "<<EOF";
    private const EOF_END_STRING = "EOF";

    private const FILE_PATH = '/app/Library/CLI/FileNameParamSampleLibrary.php';

    /**
     * test paramteter data
     * @return array
     */
    public static function sampleDataProvider(): array
    {
        return [
            'sample case' => [
                'fileName'  => './storage/csv/default/test1.csv',
            ],
        ];
    }

    /**
     * sample test.
     *
     * @param string $fileName
     * @return void
     */
    #[DataProvider('sampleDataProvider')]
    public function testSample(string $fileName): void
    {
        exec('echo $PWD', $pwd);
        $filePath = current($pwd) . self::FILE_PATH;

        // use $argv
        // exec("php {$this->testFilePath}", $result);
        exec("php $filePath" . " $fileName ", $result);

        // echo "file test result :\n";
        // echo var_dump($result);

        $this->assertIsArray($result);
    }
}
