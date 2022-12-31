<?php

namespace Tests\Unit\Library\String;

use App\Library\String\UuidLibrary;
use Tests\TestCase;
use Illuminate\Support\Facades\Config;

class UuidLibraryTest extends TestCase
{
    private const VERSION4_STRING = 4;
    // dechex()やhexdec()などを使う時は文字列にする必要がある
    private const BIN_11 = 0b1011; // 2進数の11。bit演算用
    private const HEX_11 = 0xB; // 16進数の11。
    private const HEX_125 = 0x7D; // 16進数の125。
    private const HEX_255 = 0xFF; // 16進数の255。

    /**
     * setUpは各テストメソッドが実行される前に実行する
     * 親クラスのsetUpを必ず実行する
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * test uuid version 4.
     *
     * @return void
     */
    public function testUuidVersion4(): void
    {
        $result = UuidLibrary::uuidVersion4();

        // xの位置
        // strpos(UuidLibrary::PATTERN_V4, UuidLibrary::CHAR_BIT);
        // yの位置
        // strpos(UuidLibrary::PATTERN_V4, UuidLibrary::CHAR_VARIANT);

        $this->assertEquals(
            self::VERSION4_STRING,
            mb_substr($result, mb_strpos(UuidLibrary::PATTERN_V4, self::VERSION4_STRING), 1)
        );

        $this->assertGreaterThan(
            (UuidLibrary::RANDOM_INT_MIN_8 - 1),
            mb_substr($result, mb_strpos(UuidLibrary::PATTERN_V4, UuidLibrary::CHAR_VARIANT), 1)
        );

        $this->assertLessThan(
            dechex(UuidLibrary::RANDOM_INT_MAX_11 + 1),
            mb_substr($result, mb_strpos(UuidLibrary::PATTERN_V4, UuidLibrary::CHAR_VARIANT), 1)
        );
    }
}
