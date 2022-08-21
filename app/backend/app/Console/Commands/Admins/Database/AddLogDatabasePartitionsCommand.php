<?php

namespace App\Console\Commands\Admins\Database;

use Illuminate\Support\Facades\Config;
use Illuminate\Console\Command;
use App\Console\Commands\Admins\Database\BaseAddDatabasePartitionsCommand;
use App\Models\Logs\AdminsLog;
use App\Models\Logs\BaseLogDataModel;
use App\Models\Logs\UserCoinPaymentLog;
use App\Library\Time\TimeLibrary;

class AddLogDatabasePartitionsCommand extends BaseAddDatabasePartitionsCommand
{
    /**
     * The name and signature of the console command.(コンソールコマンドの名前と使い方)
     *
     * @var string
     */
    protected $signature = 'admins:add-logs-partitions'; // if require parameter 'debug:test {param}';

    /**
     * The console command description.(コンソールコマンドの説明)
     *
     * @var string
     */
    protected $description = 'add log database paprtitions';


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

        $this->setPartitions();

        echo 'Finish.' . "\n";
    }

    /**
     * get settings for adding partition target tables.
     *
     * @return array
     */
    protected function getPartitionSettings(): array
    {
        $connection = BaseLogDataModel::getLogDatabaseConnection();

        // テーブルごとのパーティション設定
        return [
            [
                self::PRTITION_SETTING_KEY_CONNECTION_NAME            => $connection,
                self::PRTITION_SETTING_KEY_TABLE_NAME                 => (new AdminsLog())->getTable(),
                self::PRTITION_SETTING_KEY_PARTITION_TYPE             => self::PARTITION_TYPE_ID,
                self::PRTITION_SETTING_KEY_COLUMN_NAME                => AdminsLog::ID,
                self::ID_PRTITION_SETTING_KEY_TARGET_ID               => 1,
                self::ID_PRTITION_SETTING_KEY_BASE_NUMBER             => 100000,
                self::ID_PRTITION_SETTING_KEY_PARTITION_COUNT         => 10,
                self::NAME_PRTITION_SETTING_KEY_TARGET_DATE           => null,
                self::NAME_PRTITION_SETTING_KEY_PARTITION_MONTH_COUNT => null,
            ],
            [
                self::PRTITION_SETTING_KEY_CONNECTION_NAME            => $connection,
                self::PRTITION_SETTING_KEY_TABLE_NAME                 => (new UserCoinPaymentLog())->getTable(),
                self::PRTITION_SETTING_KEY_PARTITION_TYPE             => self::PARTITION_TYPE_DATE,
                self::PRTITION_SETTING_KEY_COLUMN_NAME                => UserCoinPaymentLog::CREATED_AT,
                self::ID_PRTITION_SETTING_KEY_TARGET_ID               => null,
                self::ID_PRTITION_SETTING_KEY_BASE_NUMBER             => null,
                self::ID_PRTITION_SETTING_KEY_PARTITION_COUNT         => null,
                self::NAME_PRTITION_SETTING_KEY_TARGET_DATE           => TimeLibrary::getCurrentDateTime(),
                self::NAME_PRTITION_SETTING_KEY_PARTITION_MONTH_COUNT => 3,
            ],
        ];
    }
}
