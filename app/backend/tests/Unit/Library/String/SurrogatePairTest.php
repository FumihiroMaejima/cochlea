<?php

namespace Tests\Unit\Library\String;

use App\Library\String\SurrogatePair;
use Tests\TestCase;
use Illuminate\Support\Facades\Config;

class SurrogatePairTest extends TestCase
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
     * test surrogate pair data
     * @return array
     */
    public function checkSurrogatePairDataProvider(): array
    {
        $this->createApplication();

        return [
            'simple string value' => [
                'value' => 'stringValue',
                'expect' => true,
            ],
            'is surrogate value(emoji)' => [
                'value' => '😀',
                'expect' => false,
            ],
        ];
    }

    /**
     * test check surrogatePair.
     *
     * @dataProvider checkSurrogatePairDataProvider
     * @param string $value
     * @param bool $expect
     * @return void
     */
    public function testCheckSurrogatePair(string $value, bool $expect): void
    {
        $this->assertSame($expect, SurrogatePair::isNotSurrogatePair($value));
    }
}
