<?php

namespace Tests\Unit\Library\Performance;

// use PHPUnit\Framework\TestCase;

use App\Library\Performance\MemoryLibrary;
use Tests\TestCase;
use Exception;

/**
 * Test Class of MemoryLibrary
 * *テスト実行時のメモリ使用量が急増する為注意すること
 */
class MemoryLibraryTest extends TestCase
{
    private const TEST_VALUE = '1234567abcdefg';

    /**
     * setUpは各テストメソッドが実行される前に実行する
     * 親クラスのsetUpを必ず実行する
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * test convert memory usage data
     * @return array
     */
    public function convertToMemoryUsageDataProvider(): array
    {
        $this->createApplication();

        return [
            'memory usage 100' => [
                'value' => 100,
                'expect' => '100 B',
            ],
            'memory usage 1000' => [
                'value' => 1000,
                'expect' => '1000 B',
            ],
            'memory usage 1024' => [
                'value' => 1024,
                'expect' => '1 KB',
            ],
            'memory usage 1100' => [
                'value' => 1100,
                'expect' => '1.07 KB',
            ],
            'memory usage 1048576 (1024 * 1024)' => [
                'value' => 1048576,
                'expect' => '1 MB',
            ],
        ];
    }

    /**
     * test int value list length data
     * @return array
     */
    public function intValueUsageListDataProvider(): array
    {
        $this->createApplication();

        return [
            'lenght 100' => [
                'value' => 100,
                'expect' => '2.55 KB',
            ],
            'lenght 1000' => [
                'value' => 1000,
                'expect' => '20.05 KB',
            ],
            'lenght 10000' => [
                'value' => 10000,
                'expect' => '260.05 KB',
            ],
            'lenght 100000' => [
                'value' => 100000,
                'expect' => '2 MB',
            ],
            'lenght 1000000' => [
                'value' => 1000000,
                'expect' => '16 MB',
            ],
            'lenght 10000000' => [
                'value' => 10000000,
                'expect' => '256 MB',
            ],
        ];
    }

    /**
     * test get int vakue list memory usage.
     *
     * @dataProvider convertToMemoryUsageDataProvider
     * @param int $value
     * @param string $expect
     * @return void
     */
    public function testConvert(int $value, string $expect): void
    {
        $this->assertSame($expect, MemoryLibrary::convert($value));
    }

    /**
     * test get int vakue list memory usage.
     *
     * @dataProvider intValueUsageListDataProvider
     * @param int $value
     * @param string $expect
     * @return void
     */
    public function testGetIntValueListUsage(int $value, string $expect): void
    {
        $this->assertSame($expect, MemoryLibrary::getIntValueListUsage($value));
    }
}
