<?php

namespace Tests\Unit\Library\Performance;

// use PHPUnit\Framework\TestCase;

use App\Library\Performance\MemoryLibrary;
use Tests\TestCase;
use Exception;

/**
 * Test Class of MemoryLibrary
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
                'expect' => '8.05 KB',
            ],
            'lenght 1000' => [
                'value' => 1000,
                'expect' => '36.05 KB',
            ],
            'lenght 10000' => [
                'value' => 10000,
                'expect' => '516.05 KB',
            ],
            'lenght 100000' => [
                'value' => 100000,
                'expect' => '4 MB',
            ],
            'lenght 1000000' => [
                'value' => 1000000,
                'expect' => '32 MB',
            ],
            'lenght 10000000' => [
                'value' => 10000000,
                'expect' => '512 MB',
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
