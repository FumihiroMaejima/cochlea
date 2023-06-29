<?php

namespace Tests\Unit\Library\Hash;

// use PHPUnit\Framework\TestCase;

use App\Library\Hash\HashLibrary;
use Tests\TestCase;
use Exception;

/**
 * Test Class of HashLibrary
 */
class HashLibraryTest extends TestCase
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
     * hash check data
     * @return array
     */
    public function makeHashCheckDataProvider(): array
    {
        $this->createApplication();

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
     * test crete hash.
     *
     * @dataProvider makeHashDataProvider
     * @param string $value
     * @param string $expect
     * @return void
     */
    public function testHash(string $value): void
    {
        $value1 = HashLibrary::hash($value);
        $value2 = HashLibrary::hash($value);
        $this->assertEquals($value1, $value2);
    }

    /**
     * test check hash.
     *
     * @dataProvider makeHashCheckDataProvider
     * @param string $value1
     * @param string $value2
     * @param bool $expect
     * @return void
     */
    public function testCheck(string $value1, string $value2, bool $expect): void
    {
        $hashedValue = HashLibrary::hash($value2);
        $this->assertEquals($expect, HashLibrary::check($value1, $hashedValue));
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
        $hashedValue = HashLibrary::hash($value);
        $this->assertIsArray(HashLibrary::info($hashedValue));
    }
}
