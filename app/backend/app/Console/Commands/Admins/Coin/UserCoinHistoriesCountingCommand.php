<?php

namespace App\Console\Commands\Admins\Coin;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use App\Library\Database\ShardingProxyLibrary;
use App\Library\Time\TimeLibrary;
use App\Models\Users\UserCoinHistories;

class UserCoinHistoriesCountingCommand extends Command
{
    /**
     * The name and signature of the console command.(コンソールコマンドの名前と使い方)
     *
     * @var string
     */
    protected $signature = 'admins:counting-user-coin-histories'; // if require parameter 'debug:test {param}';

    /**
     * The console command description.(コンソールコマンドの説明)
     *
     * @var string
     */
    protected $description = 'countig user coin histories record in 1 mounth';


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
        $records = self::getUserCoinHistories();
        echo var_dump($records);
    }

    /**
     * get coin histories
     *
     * @return array
     */
    private static function getUserCoinHistories(): array
    {
        $timestamp = TimeLibrary::getCurrentDateTimeTimeStamp();
        $startAt = TimeLibrary::startDayOfMonth($timestamp, TimeLibrary::DATE_TIME_FORMAT_START_DATE);
        $endAt = TimeLibrary::lastDayOfMonth($timestamp, TimeLibrary::DATE_TIME_FORMAT_END_DATE);
        return ShardingProxyLibrary::select(
            (new UserCoinHistories())->getTable(),
            betweens: [UserCoinHistories::CREATED_AT => [$startAt, $endAt]]

        );
    }
}
