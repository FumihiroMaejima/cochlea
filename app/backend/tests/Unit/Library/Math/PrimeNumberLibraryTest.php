<?php

namespace Tests\Unit\Library\Math;

// use PHPUnit\Framework\TestCase;

use App\Library\Math\PrimeNumberLibrary;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class PrimeNumberLibraryTest extends TestCase
{
    /**
     * setUpは各テストメソッドが実行される前に実行する
     * 親クラスのsetUpを必ず実行する
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * prime number data
     * @return array
     */
    public static function isPrimeNumberDataProvider(): array
    {
        $trueValue = true;
        $falseValue = false;

        return [
            "0 is false" => [
                'value' => 0,
                'expect' => $falseValue,
            ],
            "1 is false" => [
                'value' => 1,
                'expect' => $falseValue,
            ],
            "2 is true" => [
                'value' => 2,
                'expect' => $trueValue,
            ],
            "3 is true" => [
                'value' => 3,
                'expect' => $trueValue,
            ],
        ];
    }

    /**
     * max prime number data
     * @return array
     */
    public static function getMaxPrimeNumberDataProvider(): array
    {
        return [
            "0 is 0" => [
                'value' => 0,
                'expect' => 0,
            ],
            "1 is 0" => [
                'value' => 1,
                'expect' => 0,
            ],
            "2 is 2" => [
                'value' => 2,
                'expect' => 2,
            ],
            "4 is 3" => [
                'value' => 4,
                'expect' => 3,
            ],
        ];
    }

    /**
     * max greater prime numbers data
     * @return array
     */
    public static function getGreaterPrimeNumbersDataProvider(): array
    {
        return [
            "value=0/count=1/expect=[]" => [
                'value' => 0,
                'count' => 1,
                'expect' => [],
            ],
            "value=1/count=1/expect=[]" => [
                'value' => 1,
                'count' => 1,
                'expect' => [],
            ],
            "value=2/count=1/expect=[2]" => [
                'value' => 2,
                'count' => 1,
                'expect' => [2],
            ],
            "value=4/count=1/expect=[3]" => [
                'value' => 4,
                'count' => 1,
                'expect' => [3],
            ],
            "value=4/count=2/expect=[3,2]" => [
                'value' => 4,
                'count' => 2,
                'expect' => [3,2],
            ],
        ];
    }

    /**
     * test is prime number.
     *
     * @dataProvider isPrimeNumberDataProvider
     * @param int $value
     * @param bool $expect
     * @return void
     */
    public function testIsPrimeNumber(int $value, bool $expect): void
    {
        $this->assertSame($expect, PrimeNumberLibrary::isPrimeNumber($value));
    }

    /**
     * test get max prime number.
     *
     * @dataProvider getMaxPrimeNumberDataProvider
     * @param int $value
     * @param int $expect
     * @return void
     */
    public function testGetMaxPrimeNumber(int $value, int $expect): void
    {
        $this->assertSame($expect, PrimeNumberLibrary::getMaxPrimeNumber($value));
    }

    /**
     * test get greter prime numbers.
     *
     * @dataProvider getGreaterPrimeNumbersDataProvider
     * @param int $value
     * @param int $count
     * @param array $expect
     * @return void
     */
    public function testGetGreaterPrimeNumbers(int $value, int $count, array $expect): void
    {
        $this->assertSame($expect, PrimeNumberLibrary::getGreaterPrimeNumbers($value, $count));
    }
}
