<?php

namespace Tests\Unit\Service\Array;

use App\Library\Array\ArrayLibrary;
use Tests\TestCase;
use Illuminate\Support\Facades\Config;

class ArrayLibraryTest extends TestCase
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
     * array sample data
     * @return array
     */
    public function arraySampleDataProvider(): array
    {
        $this->createApplication();

        return [
            'convert array to array' => [
                [
                    'test_key11' => [
                        'test_key11' => 11,
                        'test_key12' => 12,
                        'test_key13' => 13,
                    ],
                    'test_key21' => [
                        'test_key21' => 21,
                        'test_key22' => 22,
                        'test_key23' => 23,
                    ],
                ],
            ],
            'convert object to array' => [
                [
                    'test_key11' => (object)[
                        'test_key11' => 11,
                        'test_key12' => 12,
                        'test_key13' => 13,
                    ],
                    'test_key21' => (object)[
                        'test_key21' => 21,
                        'test_key22' => 22,
                        'test_key23' => 23,
                    ],
                ],
            ],
        ];
    }

    /**
     * test to array.
     *
     * @dataProvider arraySampleDataProvider
     * @return void
     */
    public function testToArray(array $data): void
    {
        $result = ArrayLibrary::toArray($data);

        // echo var_dump($data);
        // echo var_dump($result);

        $this->assertEquals(json_decode(json_encode($data), true), $result);
        // 一番先頭の要素
        $this->assertIsArray(current($result));
    }

    /**
     * test get first in array items.
     *
     * @dataProvider arraySampleDataProvider
     * @return void
     */
    public function testGetFirst(array $data): void
    {
        $result = ArrayLibrary::toArray($data);

        // echo var_dump($data);
        // echo var_dump($result);

        $this->assertEquals(current(json_decode(json_encode($data), true)), ArrayLibrary::getFirst($result));
    }
}