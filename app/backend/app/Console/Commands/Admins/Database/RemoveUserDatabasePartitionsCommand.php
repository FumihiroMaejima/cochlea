<?php

namespace App\Console\Commands\Admins\Database;

use Illuminate\Support\Facades\Config;
use Illuminate\Console\Command;
use App\Console\Commands\Admins\Database\BaseDatabasePartitionsCommand;
use App\Library\Time\TimeLibrary;

class RemoveUserDatabasePartitionsCommand extends BaseDatabasePartitionsCommand
{
    /**
     * The name and signature of the console command.(コンソールコマンドの名前と使い方)
     *
     * @var string
     */
    protected $signature = 'admins:remove-users-partitions'; // if require parameter 'debug:test {param}';

    /**
     * The console command description.(コンソールコマンドの説明)
     *
     * @var string
     */
    protected $description = 'remove users database paprtitions';


    /**
     * インスタンスの生成
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
        echo 'Setting Partitions.' . "\n";
        echo 'Date: ' . TimeLibrary::getCurrentDateTime() . "\n";

        $this->removePartitions();

        echo 'Finish.' . "\n";
    }

    /**
     * get settings for partition target tables.
     *
     * @return array
     */
    protected function getPartitionSettings(): array
    {
        // ユーザー系テーブルの設定を取得
        return $this->getUserDatabsePartitionSettings();
    }
}
