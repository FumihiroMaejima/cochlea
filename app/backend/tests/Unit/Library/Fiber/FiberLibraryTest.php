<?php

namespace Tests\Unit\Library\Fiber;

use App\Library\Array\ArrayLibrary;
use App\Library\Fiber\FiberLibrary;
use Tests\TestCase;
use Illuminate\Support\Facades\Config;

class FiberLibraryTest extends TestCase
{

    private const MUITIDIMENTIONAL_ARRAY_KEY_ID = 'id';
    private const MUITIDIMENTIONAL_ARRAY_KEY_KEY1 = 'key1';
    private const MUITIDIMENTIONAL_ARRAY_KEY_KEY2 = 'key2';

    /**
     * setUpは各テストメソッドが実行される前に実行する
     * 親クラスのsetUpを必ず実行する
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * get fiber data
     * @return array
     */
    public static function getFiberDataProvider(): array
    {
        return [
            'value=1/return=6' => [
                'value'  => 1,
                'expect' => 6,
            ],
        ];
    }

    /**
     * test get fiber.
     *
     * @dataProvider getFiberDataProvider
     * @param int $value
     * @param int $expect
     * @return void
     */
    public function testGetFiber(int $value, int $expect): void
    {
        $fiber = FiberLibrary::getFiber($value);
        $fiber->start($value);
        $fiber->resume(++$value);
        $fiber->resume(++$value);
        $fiber->resume(++$value);
        $fiber->resume(++$value);
        $fiber->resume(++$value);

        $this->assertEquals($expect, $fiber->getReturn());
    }
}
