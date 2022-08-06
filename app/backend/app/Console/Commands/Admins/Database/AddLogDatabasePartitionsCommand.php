<?php

namespace App\Console\Commands\Admins\Database;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use App\Console\Commands\Admins\Database\BaseAddDatabasePartitionsCommand;
use App\Exceptions\MyApplicationHttpException;
use App\Exceptions\ExceptionStatusCodeMessages;
use App\Models\Logs\AdminsLog;
use App\Models\Logs\BaseLogDataModel;
use App\Models\Logs\UserCoinPaymentLog;
use App\Library\Time\TimeLibrary;


class AddLogDatabasePartitionsCommand extends BaseAddDatabasePartitionsCommand
{
    // information schema table name.
    private const INFORMATION_SCHEMA_PARTITIONS_TABLE_NAME = 'INFORMATION_SCHEMA.PARTITIONS';

    // record offset (1 record)
    private const PRTITION_OFFSET_VALUE = 1;

    // partition setting key
    private const PRTITION_SETTING_KEY_CONNECTION_NAME = 'databaseName';
    private const PRTITION_SETTING_KEY_TABLE_NAME = 'tableName';
    private const PRTITION_SETTING_KEY_PARTITION_TYPE = 'partitionYype';
    private const PRTITION_SETTING_KEY_COLUMN_NAME = 'columnName';

    // パーティションタイプごとの詳細な設定
    private const ID_PRTITION_SETTING_KEY_TARGET_ID = 'targetId'; // パーティション数の起算ID
    private const ID_PRTITION_SETTING_KEY_BASE_NUMBER = 'baseNumber'; // 1パーティション値りのID数
    private const ID_PRTITION_SETTING_KEY_PARTITION_COUNT = 'partitionCount'; // パーティション数
    private const NAME_PRTITION_SETTING_KEY_TARGET_DATE = 'targetDate'; // パーティション数の起算日
    private const NAME_PRTITION_SETTING_KEY_PARTITION_MONTH_COUNT = 'mounthCount'; // パーティション数(1パーティション=1日を月数で設定)

    // partition type
    private const PARTITION_TYPE_ID = 1;
    private const PARTITION_TYPE_DATE = 2;

    private const PARTITION_TYPES = [
        self::PARTITION_TYPE_ID,
        self::PARTITION_TYPE_DATE
    ];

    private const ALTER_TABLE_TYPE_CREATE = 'create';
    private const ALTER_TABLE_TYPE_ADD = 'add';

    private const ALTER_TABLE_TYPES = [
        self::ALTER_TABLE_TYPE_CREATE,
        self::ALTER_TABLE_TYPE_ADD
    ];

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
    public function getPartitionSettings(): array
    {
        $connection = BaseLogDataModel::setConnectionName();

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
