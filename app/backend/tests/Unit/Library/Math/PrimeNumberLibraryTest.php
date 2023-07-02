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
    public function isPrimeNumberDataProvider(): array
    {
        $this->createApplication();

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
    public function getMaxPrimeNumberDataProvider(): array
    {
        $this->createApplication();

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
    public function getGreaterPrimeNumbersDataProvider(): array
    {
        $this->createApplication();

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
     * max get prime factorization data
     * @return array
     */
    public function getPrimeFactorizationDataProvider(): array
    {
        $this->createApplication();

        return [
            "value is 30" => [
                'value' => 30,
                'expect' => [2,3,5],
            ],
            "value is 33" => [
                'value' => 33,
                'expect' => [3,11],
            ],
            "value is 84" => [
                'value' => 84,
                'expect' => [2,2,3,7],
            ],
            "value is 100" => [
                'value' => 100,
                'expect' => [2,2,5,5],
            ],
            "value is 200" => [
                'value' => 200,
                'expect' => [2,2,2,5,5],
            ],
        ];
    }

    /**
     * max get greated common divisor data
     * @return array
     */
    public function getGreatestCommonDivisorDataProvider(): array
    {
        $this->createApplication();

        return [
            "value1=3/value2=7/expect=1" => [
                'value1' => 3,
                'value2' => 7,
                'expect' => 1,
            ],
            "value1=5/value2=15/expect=5" => [
                'value1' => 5,
                'value2' => 15,
                'expect' => 5,
            ],
            "value1=100/value2=24/expect=4" => [
                'value1' => 100,
                'value2' => 24,
                'expect' => 4,
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

    /**
     * test get prime factorization.
     *
     * @dataProvider getPrimeFactorizationDataProvider
     * @param int $value
     * @param array $expect
     * @return void
     */
    public function testGetPrimeFactorization(int $value, array $expect): void
    {
        $this->assertSame($expect, PrimeNumberLibrary::getPrimeFactorization($value));
    }

    /**
     * test get greatest common divisor.
     *
     * @dataProvider getGreatestCommonDivisorDataProvider
     * @param int $value1
     * @param int $value2
     * @param int $expect
     * @return void
     */
    public function testGetGreatestCommonDivisor(int $value1, int $value2, int $expect): void
    {
        $this->assertSame($expect, PrimeNumberLibrary::getGreatestCommonDivisor($value1, $value2));
    }
}
