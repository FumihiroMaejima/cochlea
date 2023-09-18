<?php

namespace Tests\Unit\Library\Array;

use App\Library\Array\ArrayLibrary;
use Tests\TestCase;
use Illuminate\Support\Facades\Config;

class ArrayLibraryTest extends TestCase
{

    private const MUITIDIMENTIONAL_ARRAY_KEY_ID = 'id';
    private const MUITIDIMENTIONAL_ARRAY_KEY_KEY1 = 'key1';
    private const MUITIDIMENTIONAL_ARRAY_KEY_KEY2 = 'key2';
    private const MUITIDIMENTIONAL_ARRAY_TEMPLATE = [
        self::MUITIDIMENTIONAL_ARRAY_KEY_ID => 1,
        self::MUITIDIMENTIONAL_ARRAY_KEY_KEY1 => 1,
        self::MUITIDIMENTIONAL_ARRAY_KEY_KEY2 => 'id=',
    ];

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
    public static function arraySampleDataProvider(): array
    {
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
     * array paging data
     * @return array
     */
    public static function pagingDataProvider(): array
    {
        $testArray = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

        // 多次元配列
        $testMultidimensionalAarray = [];
        foreach (range(1, 10) as $i) {
            $tmp = self::MUITIDIMENTIONAL_ARRAY_TEMPLATE;
            $tmp[self::MUITIDIMENTIONAL_ARRAY_KEY_ID] = $i;
            $tmp[self::MUITIDIMENTIONAL_ARRAY_KEY_KEY1] = $i;
            $tmp[self::MUITIDIMENTIONAL_ARRAY_KEY_KEY2] .= $i;
            $testMultidimensionalAarray[] = $tmp;
        }

        return [
            'page:0/limit:3/result:[1,2,3]' => [
                'items'  => $testArray,
                'page'   => 0,
                'limit'  => 3,
                'expect' => [1, 2, 3],
            ],
            'page:1/limit:3/result:[4,5,6]' => [
                'items'  => $testArray,
                'page'   => 1,
                'limit'  => 3,
                'expect' => [4, 5, 6],
            ],
            'page:3/limit:3/result:[3]' => [
                'items'  => $testArray,
                'page'   => 3,
                'limit'  => 3,
                'expect' => [10],
            ],
            'page:2/limit:4/result:[9,10]' => [
                'items'  => $testArray,
                'page'   => 2,
                'limit'  => 4,
                'expect' => [9,10],
            ],
            'page:4/limit:3/result:[]' => [
                'items'  => $testArray,
                'page'   => 4,
                'limit'  => 3,
                'expect' => [],
            ],
            'page:0/limit:null/result:origin' => [
                'items'  => $testArray,
                'page'   => 0,
                'limit'  => null,
                'expect' => $testArray,
            ],
            'multiDimentionalArray/page:0/limit:3' => [
                'items'  => $testMultidimensionalAarray,
                'page'   => 0,
                'limit'  => 3,
                'expect' => [
                    $testMultidimensionalAarray[0],
                    $testMultidimensionalAarray[1],
                    $testMultidimensionalAarray[2],
                ],
            ],
            'multiDimentionalArray/page:1/limit:3' => [
                'items'  => $testMultidimensionalAarray,
                'page'   => 1,
                'limit'  => 3,
                'expect' => [
                    $testMultidimensionalAarray[3],
                    $testMultidimensionalAarray[4],
                    $testMultidimensionalAarray[5],
                ],
            ],
            'multiDimentionalArray/page:3/limit:3' => [
                'items'  => $testMultidimensionalAarray,
                'page'   => 3,
                'limit'  => 3,
                'expect' => [
                    $testMultidimensionalAarray[9],
                ],
            ],
            'multiDimentionalArray/page:1/limit:3' => [
                'items'  => $testMultidimensionalAarray,
                'page'   => 4,
                'limit'  => 3,
                'expect' => [],
            ],
            'multiDimentionalArray/page:0/limit:null/result:origin' => [
                'items'  => $testMultidimensionalAarray,
                'page'   => 0,
                'limit'  => null,
                'expect' => $testMultidimensionalAarray,
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

    /**
     * test paging array items.
     *
     * @dataProvider pagingDataProvider
     * @return void
     */
    public function testPaging(array $items, int $page, ?int $limit, array $expect): void
    {
        $this->assertEquals($expect, ArrayLibrary::paging($items, $page, $limit));
    }
}
