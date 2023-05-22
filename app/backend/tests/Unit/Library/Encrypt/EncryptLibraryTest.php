<?php

namespace Tests\Unit\Library\Encrypt;

// use PHPUnit\Framework\TestCase;

use App\Library\Encrypt\EncryptLibrary;
use Tests\TestCase;

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
     * encrypt stringdata data
     * @return array
     */
    public function encryptStringDataProvider(): array
    {
        $this->createApplication();

        return [
            'encrypt email' => [
                'value' => self::TEST_EMAIL_VALUE,
                'expect' => self::TEST_ENCRYPT_EMAIL_VALUE,
            ],
        ];
    }

    /**
     * test encrypt string.
     *
     * @dataProvider encryptStringDataProvider
     * @param string $value
     * @param string $expect
     * @return void
     */
    public function testEncrypt($value, $expect): void
    {
        $this->assertStringStartsWith($expect, EncryptLibrary::encrypt($value, false));
    }
}
