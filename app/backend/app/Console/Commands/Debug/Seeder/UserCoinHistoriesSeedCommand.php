<?php

namespace App\Console\Commands\Debug\Seeder;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use App\Library\Performance\MemoryLibrary;
use App\Library\Time\TimeLibrary;
use Database\Seeders\Users\UserCoinHistoriesTableSeeder;

class UserCoinHistoriesSeedCommand extends Command
{
    /**
     * The name and signature of the console command.(コンソールコマンドの名前と使い方)
     *
     * @var string
     */
    protected $signature = 'debug:seed-user-coin-histories'; // if require parameter 'debug:test {param}';

    /**
     * The console command description.(コンソールコマンドの説明)
     *
     * @var string
     */
    protected $description = 'debug insert user coin histories';


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
        echo TimeLibrary::getCurrentDateTime() . "\n";

        $startMemory = memory_get_usage();
        Artisan::call('db:seed', ['--class' => UserCoinHistoriesTableSeeder::class, '--no-interaction' => true]);

        // メモリ使用量出力
        MemoryLibrary::echoMemoryUsageInScript($startMemory);
    }
}
