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

}
