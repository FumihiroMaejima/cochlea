<?php

namespace Tests\Unit\Library\Encrypt;

// use PHPUnit\Framework\TestCase;

use App\Library\Encrypt\EncryptLibrary;
use Tests\TestCase;
use Exception;

/**
 * Test Class of EncryptLibrary
 */
class EncryptLibraryTest extends TestCase
{
    private const TEST_EMAIL_VALUE = 'test@example.com';
    private const TEST_ENCRYPT_EMAIL_VALUE = 'XyLyJ0jGX4VWniEcl6igwatZIl4dd+5qksaqiEdlsrA=';
    /**
     * setUpは各テストメソッドが実行される前に実行する
     * 親クラスのsetUpを必ず実行する
     */
    protected function setUp(): void
    {
        parent::setUp();
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
     * decrypt string data
     * @return array
     */
    public function decryptStringDataProvider(): array
    {
        $this->createApplication();

        return [
            'decrypt email for ecb mode data' => [
                'value' => self::TEST_ENCRYPT_EMAIL_VALUE,
                'expect' => self::TEST_EMAIL_VALUE,
            ],
        ];
    }

    /**
     * create iv length data
     * @return array
     */
    public function createIvDataProvider(): array
    {
        $this->createApplication();

        return [
            /* 'length 0' => [
                'length' => 0,
            ], */
            'length 1' => [
                'length' => 1,
            ],
            'length 5' => [
                'length' => 5,
            ],
            'length 10' => [
                'length' => 10,
            ],
            'length 16' => [
                'length' => 16,
            ],
        ];
    }

    /**
     * test encrypt string by ECB mode.
     *
     * @dataProvider encryptStringDataProvider
     * @param string $value
     * @param string $expect
     * @return void
     */
    public function testEncryptByEbcMode(string $value, string $expect): void
    {
        $this->assertSame($expect, EncryptLibrary::encrypt($value, false));
    }

    /**
     * test encrypt string by CBC mode.
     *
     * @dataProvider encryptStringDataProvider
     * @param string $value
     * @param string $expect
     * @return void
     */
    public function testEncryptByCbcMode(string $value, string $expect): void
    {
        // EBCモードとは値が異なる
        $this->assertNotSame($expect, EncryptLibrary::encrypt($value, true));
    }

    /**
     * test decrypt string by ECB mode.
     *
     * @dataProvider decryptStringDataProvider
     * @param string $value
     * @param string $expect
     * @return void
     */
    public function testDecryptByEbcMode(string $value, string $expect): void
    {
        $this->assertSame($expect, EncryptLibrary::decrypt($value, false));
    }

    /**
     * test decrypt string by CBC mode.
     *
     * @dataProvider decryptStringDataProvider
     * @param string $value
     * @param string $expect
     * @return void
     */
    public function testDecryptByCbcMode(string $value, string $expect): void
    {
        $this->assertNotSame($expect, EncryptLibrary::decrypt($value, true));
    }

    /**
     * test create iv method output is random.
     *
     * @dataProvider createIvDataProvider
     * @param int $length
     * @param Exception|null $exception
     * @return void
     */
    public function testCreateIvRandomValue(int $length, Exception $exception = null): void
    {
        /* if (!is_null($exception)) {
            $this->assertThrows(, $exception);
        } */
        $value = EncryptLibrary::createIv($length);
        $expect = EncryptLibrary::createIv($length);
        $this->assertNotSame($expect, $value);
    }
}
