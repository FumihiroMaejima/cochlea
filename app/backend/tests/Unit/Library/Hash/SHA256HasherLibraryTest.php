<?php

namespace Tests\Unit\Library\Hash;

// use PHPUnit\Framework\TestCase;

use App\Library\Hash\SHA256HasherLibrary;
use Tests\TestCase;
use Exception;

/**
 * Test Class of SHA256HasherLibrary
 */
class SHA256HasherLibraryTest extends TestCase
{
    private const TEST_EMAIL_VALUE = 'test@example.com';
    private const TEST_ENCRYPT_EMAIL_VALUE = 'XyLyJ0jGX4VWniEcl6igwatZIl4dd+5qksaqiEdlsrA=';

    private const TEST_HASH_TARGET_VALUE = 'testValue12345';
    /**
     * setUpは各テストメソッドが実行される前に実行する
     * 親クラスのsetUpを必ず実行する
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * hash string data
     * @return array
     */
    public static function makeHashDataProvider(): array
    {
        return [
            'string value' => [
                'value' => self::TEST_HASH_TARGET_VALUE,
            ],
        ];
    }

    /**
     * hash check data
     * @return array
     */
    public static function makeHashCheckDataProvider(): array
    {
        return [
            'same hash value' => [
                'value1' => self::TEST_HASH_TARGET_VALUE,
                'value2' => self::TEST_HASH_TARGET_VALUE,
                'expect' => true,
            ],
            'different hash value' => [
                'value1' => self::TEST_HASH_TARGET_VALUE,
                'value2' => 'test12345',
                'expect' => false,
            ],
        ];
    }

    /**
     * encrypt string data
     * @return array
     */
    public static function encryptStringDataProvider(): array
    {
        return [
            'encrypt email for ecb mode data' => [
                'value' => self::TEST_EMAIL_VALUE,
                'expect' => self::TEST_ENCRYPT_EMAIL_VALUE,
            ],
        ];
    }

    /**
     * test hash make hash.
     *
     * @dataProvider makeHashDataProvider
     * @param string $value
     * @param string $expect
     * @return void
     */
    public function testMake(string $value): void
    {
        $value1 = SHA256HasherLibrary::make($value);
        $value2 = SHA256HasherLibrary::make($value);
        $this->assertEquals($value1, $value2);
    }

    /**
     * test hash check hash.
     *
     * @dataProvider makeHashCheckDataProvider
     * @param string $value1
     * @param string $value2
     * @param bool $expect
     * @return void
     */
    public function testCheck(string $value1, string $value2, bool $expect): void
    {
        $hashedValue = SHA256HasherLibrary::make($value2);
        $this->assertEquals($expect, SHA256HasherLibrary::check($value1, $hashedValue));
    }

    /**
     * test password get info.
     *
     * @dataProvider makeHashDataProvider
     * @param string $value
     * @return void
     */
    public function testPasswordGetInfo(string $value): void
    {
        $hashedValue = SHA256HasherLibrary::make($value);
        $this->assertIsArray(SHA256HasherLibrary::info($hashedValue));
    }
}
