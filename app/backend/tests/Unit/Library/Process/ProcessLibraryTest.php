<?php

namespace Tests\Unit\Library\Process;

use App\Library\Fiber\FiberLibrary;
use App\Library\Process\ProcessLibrary;
use Tests\TestCase;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Process\Process;

class ProcessLibraryTest extends TestCase
{

    private const MUITIDIMENTIONAL_ARRAY_KEY_ID = 'id';
    private const MUITIDIMENTIONAL_ARRAY_KEY_KEY1 = 'key1';
    private const MUITIDIMENTIONAL_ARRAY_KEY_KEY2 = 'key2';

    /**
     * setUpは各テストメソッドが実行される前に実行する
     * 親クラスのsetUpを必ず実行する
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * get sample process data
     * @return array
     */
    public static function sampleProcessDataProvider(): array
    {
        return [
            'value=test1/return=test1' => [
                'value'  => 'test1',
                'expect' => 'catch resume test1',
            ],
        ];
    }

    /**
     * test sample process.
     *
     * @dataProvider sampleProcessDataProvider
     * @param string $value
     * @param string $expect
     * @return void
     */
    public function testSampleProcess(string $value, string $expect): void
    {
        $result = ProcessLibrary::sampleProcess();
        $this->assertEquals('.', current(explode("\n", $result)));
    }
}
