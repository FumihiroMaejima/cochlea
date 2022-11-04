<?php

namespace App\Console\Commands\Admins\Database;

use Illuminate\Support\Facades\Config;
use Illuminate\Console\Command;
use App\Console\Commands\Admins\Database\BaseDatabasePartitionsCommand;
use App\Models\Users\BaseUserDataModel;
use App\Models\Users\UserCoinHistories;
use App\Models\Users\UserCoinPaymentStatus;
use App\Models\Users\UserReadInformations;
use App\Library\Database\ShardingLibrary;
use App\Library\Time\TimeLibrary;

class AddUserDatabasePartitionsCommand extends BaseDatabasePartitionsCommand
{
    /**
     * The name and signature of the console command.(コンソールコマンドの名前と使い方)
     *
     * @var string
     */
    protected $signature = 'admins:add-users-partitions'; // if require parameter 'debug:test {param}';

    /**
     * The console command description.(コンソールコマンドの説明)
     *
     * @var string
     */
    protected $description = 'add users database paprtitions';


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
        $partitionSettings = [];
        $dateTime = TimeLibrary::getCurrentDateTime();

        // テーブルごとのパーティション設定
        foreach (ShardingLibrary::getShardingSetting() as $node => $shardIds) {
            $connection = ShardingLibrary::getConnectionByNodeNumber($node);

            foreach ($shardIds as $shardId) {
                $partitionSettings[] = [
                    self::PRTITION_SETTING_KEY_CONNECTION_NAME            => $connection,
                    self::PRTITION_SETTING_KEY_TABLE_NAME                 => (new UserCoinHistories())->getTable().$shardId,
                    self::PRTITION_SETTING_KEY_PARTITION_TYPE             => self::PARTITION_TYPE_DATE,
                    self::PRTITION_SETTING_KEY_COLUMN_NAME                => UserCoinHistories::CREATED_AT,
                    self::ID_PRTITION_SETTING_KEY_TARGET_ID               => null,
                    self::ID_PRTITION_SETTING_KEY_BASE_NUMBER             => null,
                    self::ID_PRTITION_SETTING_KEY_PARTITION_COUNT         => null,
                    self::NAME_PRTITION_SETTING_KEY_TARGET_DATE           => $dateTime,
                    self::NAME_PRTITION_SETTING_KEY_PARTITION_MONTH_COUNT => 3,
                    self::NAME_PRTITION_SETTING_KEY_STORE_MONTH_COUNT     => null,
                ];
                $partitionSettings[] = [
                    self::PRTITION_SETTING_KEY_CONNECTION_NAME            => $connection,
                    self::PRTITION_SETTING_KEY_TABLE_NAME                 => (new UserCoinPaymentStatus())->getTable().$shardId,
                    self::PRTITION_SETTING_KEY_PARTITION_TYPE             => self::PARTITION_TYPE_DATE,
                    self::PRTITION_SETTING_KEY_COLUMN_NAME                => UserCoinPaymentStatus::CREATED_AT,
                    self::ID_PRTITION_SETTING_KEY_TARGET_ID               => null,
                    self::ID_PRTITION_SETTING_KEY_BASE_NUMBER             => null,
                    self::ID_PRTITION_SETTING_KEY_PARTITION_COUNT         => null,
                    self::NAME_PRTITION_SETTING_KEY_TARGET_DATE           => $dateTime,
                    self::NAME_PRTITION_SETTING_KEY_PARTITION_MONTH_COUNT => 3,
                    self::NAME_PRTITION_SETTING_KEY_STORE_MONTH_COUNT     => null,
                ];
                $partitionSettings[] = [
                    self::PRTITION_SETTING_KEY_CONNECTION_NAME            => $connection,
                    self::PRTITION_SETTING_KEY_TABLE_NAME                 => (new UserReadInformations())->getTable().$shardId,
                    self::PRTITION_SETTING_KEY_PARTITION_TYPE             => self::PARTITION_TYPE_DATE,
                    self::PRTITION_SETTING_KEY_COLUMN_NAME                => UserCoinPaymentStatus::CREATED_AT,
                    self::ID_PRTITION_SETTING_KEY_TARGET_ID               => null,
                    self::ID_PRTITION_SETTING_KEY_BASE_NUMBER             => null,
                    self::ID_PRTITION_SETTING_KEY_PARTITION_COUNT         => null,
                    self::NAME_PRTITION_SETTING_KEY_TARGET_DATE           => $dateTime,
                    self::NAME_PRTITION_SETTING_KEY_PARTITION_MONTH_COUNT => 3,
                    self::NAME_PRTITION_SETTING_KEY_STORE_MONTH_COUNT     => null,
                ];
            }

            // $partitionSettings = array_merge($partitionSettings, $subPartitionSettings);
        }

        return $partitionSettings;
    }
}
