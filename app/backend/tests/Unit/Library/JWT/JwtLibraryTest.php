<?php

namespace Tests\Unit\Library\JWT;

// use PHPUnit\Framework\TestCase;

use App\Library\JWT\JwtLibrary;
use Tests\TestCase;
use Exception;

/**
 * Test Class of JwtLibrary
 */
class JwtLibraryTest extends TestCase
{
    private const TEST_VALUE = '1234567abcdefg';

    /**
     * setUpは各テストメソッドが実行される前に実行する
     * 親クラスのsetUpを必ず実行する
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * decode token data
     * @return array
     */
    public function decodeTokenHeaderDataProvider(): array
    {
        $this->createApplication();

        return [
            'decode string' => [
                'value' => self::TEST_VALUE,
            ],
        ];
    }

    /**
     * encode token data
     * @return array
     */
    public function encodeTokenHeaderDataProvider(): array
    {
        $this->createApplication();

        return [
            'encode token header value' => [
                'value' => '{"typ":"JWT","alg":"none"}',
            ],
        ];
    }

    /**
     * test decode token header.
     *
     * @dataProvider decodeTokenHeaderDataProvider
     * @param string $value
     * @return void
     */
    public function testDecodeTokenHeader(string $value): void
    {
        $decodeValue = JwtLibrary::decodeTokenHeader($value);
        $expect = JwtLibrary::decodeTokenHeader($value);
        $this->assertSame($expect, $decodeValue);
    }

    /**
     * test decode token payload.
     *
     * @dataProvider decodeTokenHeaderDataProvider
     * @param string $value
     * @return void
     */
    public function testDecodeTokenPayload(string $value): void
    {
        $decodeValue = JwtLibrary::decodeTokenPayload($value);
        $expect = JwtLibrary::decodeTokenPayload($value);
        $this->assertSame($expect, $decodeValue);
    }

    /**
     * test encode token header.
     *
     * @dataProvider encodeTokenHeaderDataProvider
     * @param string $value
     * @return void
     */
    public function testEncodeTokenPayload(string $value): void
    {
        $decodeValue = JwtLibrary::encodeTokenHeader($value);
        $expect = JwtLibrary::encodeTokenHeader($value);
        $this->assertSame($expect, $decodeValue);
    }

}
