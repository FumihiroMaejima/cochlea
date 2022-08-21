<?php

namespace Tests\Unit\Service\String;

use App\Library\String\UuidLibrary;
use Tests\TestCase;
use Illuminate\Support\Facades\Config;

class UuidLibraryTest extends TestCase
{
    private const VERSION4_STRING = 4;
    private const HEX_11 = 0b1011;

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
