<?php

namespace Tests\Unit\Library\File;

// use PHPUnit\Framework\TestCase;

use App\Library\File\FileLibrary;
use Tests\TestCase;

class FileLibraryTest extends TestCase
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
    public function getQRCodeByUrlDataProvider(): array
    {
        $this->createApplication();

        return [
            'testing env is local dick' => [
                'expect' => FileLibrary::STORAGE_DISK_LOCAL,
            ],
        ];
    }

    /**
     * test get storage disk by env.
     *
     * @dataProvider getQRCodeByUrlDataProvider
     * @param string $$expect
     * @return void
     */
    public function testGetStorageDiskByEnv(string $expect): void
    {
        $this->assertEquals($expect, FileLibrary::getStorageDiskByEnv());
    }
}
