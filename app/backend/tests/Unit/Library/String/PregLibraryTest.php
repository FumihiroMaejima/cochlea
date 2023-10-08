<?php

namespace Tests\Unit\Library\String;

use App\Library\String\PregLibrary;
use Tests\TestCase;

class PregLibraryTest extends TestCase
{
    /**
     * test filtering by number data.
     * @return array
     */
    public static function filteringByNumberDataProvider(): array
    {
        return [
            'test123/123' => [
                'value' => 'test123',
                'expect' => 123,
            ],
            '1a2b3c/123' => [
                'value' => '1a2b3c',
                'expect' => 123,
            ],
            'test/null' => [
                'value' => 'test',
                'expect' => null,
            ],
            '/null' => [
                'value' => '',
                'expect' => null,
            ],
        ];
    }

    /**
     * test filtering by number.
     *
     * @dataProvider filteringByNumberDataProvider
     * @param string $value
     * @param ?int $expect
     * @return void
     */
    public function testFilteringByNumber(string $value, ?int $expect): void
    {
        $this->assertEquals(
            PregLibrary::filteringByNumber($value),
            $expect
        );
    }
}
