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
                    [
                        'test_key21' => 21,
                        'test_key22' => 22,
                        'test_key23' => 23,
                    ],
                    [
                        'test_key21' => 21,
                        'test_key22' => 22,
                        'test_key23' => 23,
                    ],
                ],
            ]
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
        echo var_dump($data);

        $this->assertEquals(json_decode(json_encode($data), true), ArrayLibrary::toArray($data));
    }
}
