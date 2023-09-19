<?php

namespace Tests\Unit\Library\File;

// use PHPUnit\Framework\TestCase;

use App\Library\File\QRCodeLibrary;
use Tests\TestCase;

class QRCodeLibraryTest extends TestCase
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
     * test get QRCode data
     * @return array
     */
    public static function getQRCodeByUrlDataProvider(): array
    {
        $url = 'http://localhost/test';

        return [
            'param url, expect html' => [
                'value' => $url,
            ],
        ];
    }

    /**
     * test get qr code by url.
     *
     * @dataProvider getQRCodeByUrlDataProvider
     * @param string $value
     * @return void
     */
    public function testGetQRCodeByUrl(string $value): void
    {
        $this->assertIsString(QRCodeLibrary::getQrCodeByUrl($value));
    }
}
