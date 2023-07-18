<?php

namespace Tests\Unit\Library\Math;

// use PHPUnit\Framework\TestCase;

use App\Library\Math\MathLibrary;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class MathLibraryTest extends TestCase
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
     * test get prime factorization.
     *
     * @dataProvider getPrimeFactorizationDataProvider
     * @param int $value
     * @param array $expect
     * @return void
     */
    public function testGetPrimeFactorization(int $value, array $expect): void
    {
        $this->assertSame($expect, MathLibrary::getPrimeFactorization($value));
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
        $this->assertSame($expect, MathLibrary::getGreatestCommonDivisor($value1, $value2));
    }
}
