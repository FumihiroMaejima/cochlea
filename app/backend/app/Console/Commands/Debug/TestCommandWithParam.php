<?php

namespace App\Console\Commands\Debug;

use Illuminate\Console\Command;

class TestCommandWithParam extends Command
{
    /**
     * The name and signature of the console command.(コンソールコマンドの名前と使い方)
     *
     * @var string
     */
    protected $signature = 'debug:test1 {paramName}'; // if require parameter 'debug:test {param}';

    /**
     * The console command description.(コンソールコマンドの説明)
     *
     * @var string
     */
    protected $description = 'debug test command with parameter.';


    /**
     * DebugTestCommandインスタンスの生成
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.(コマンドの実行)
     *
     * @return void
     */
    public function handle(): void
    {
        $value = $this->argument('paramName');
        // 現在日時(タイムゾーン付き)
        echo date('c') . "\n";
        echo $value . "\n";
    }
}
