<?php

declare(strict_types=1);

namespace App\Console\Commands\Debug;

use Illuminate\Console\Command;

class TestCommandWithParam extends Command
{
    /**
     * The name and signature of the console command.(コンソールコマンドの名前と使い方)
     *
     * @var string
     */
    protected $signature = 'debug:test1 {paramName} {--o=}'; // if require parameter 'debug:test {param}, option is {--o=}';
    // protected $signature = 'debug:test1 {params*} {--o=*}'; // if require some parameters, & options';

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
        $option = $this->option('o');
        // 全ての引数を配列で取得する場合
        // $arguments = $this->arguments();
        // すべてのオプションを配列として取得する場合
        // $options = $this->options();

        // 現在日時(タイムゾーン付き)
        echo date('c') . "\n";
        echo $value . "\n";
        echo $option . "\n";
    }
}
