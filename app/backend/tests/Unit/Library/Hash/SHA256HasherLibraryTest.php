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
    public function makeHashDataProvider(): array
    {
        $this->createApplication();

        return [
            'string value' => [
                'value' => self::TEST_HASH_TARGET_VALUE,
            ],
        ];
    }

    /**
     * encrypt string data
     * @return array
     */
    public function encryptStringDataProvider(): array
    {
        $this->createApplication();

        return [
            'encrypt email for ecb mode data' => [
                'value' => self::TEST_EMAIL_VALUE,
                'expect' => self::TEST_ENCRYPT_EMAIL_VALUE,
            ],
        ];
    }

    /**
     * test make hash.
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
}
