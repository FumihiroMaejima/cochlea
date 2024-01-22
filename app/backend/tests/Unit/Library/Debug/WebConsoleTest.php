<?php

namespace Tests\Unit\Library\Debug;

use App\Library\Debug\WebConsole;
use Tests\TestCase;

class WebConsoleTest extends TestCase
{

    private const MUITIDIMENTIONAL_ARRAY_KEY_ID = 'id';
    private const MUITIDIMENTIONAL_ARRAY_KEY_KEY1 = 'key1';
    private const MUITIDIMENTIONAL_ARRAY_KEY_KEY2 = 'key2';
    private const MUITIDIMENTIONAL_ARRAY_TEMPLATE = [
        self::MUITIDIMENTIONAL_ARRAY_KEY_ID => 1,
        self::MUITIDIMENTIONAL_ARRAY_KEY_KEY1 => 1,
        self::MUITIDIMENTIONAL_ARRAY_KEY_KEY2 => 'id=',
    ];

    /**
     * setUpは各テストメソッドが実行される前に実行する
     * 親クラスのsetUpを必ず実行する
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * exec web console data
     * @return array
     */
    public static function execDataProvider(): array
    {
        return [
            'command ls exec' => [
                'ls',
                [],
            ],
        ];
    }

    /**
     * test exec web console.
     *
     * @dataProvider execDataProvider
     * @param string $value
     * @param array $expect
     * @return void
     */
    public function testExec(string $value, array $expect): void
    {
        $result = WebConsole::exec($value);
        $this->assertIsArray($result);
    }
}
