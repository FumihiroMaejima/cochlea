<?php

namespace App\Console\Commands\Admins\Database;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use App\Exceptions\MyApplicationHttpException;
use App\Exceptions\ExceptionStatusCodeMessages;
use App\Models\Logs\AdminsLog;
use App\Models\Logs\BaseLogDataModel;
use App\Models\Logs\UserCoinPaymentLog;
use App\Library\Time\TimeLibrary;


class CheckLogDatabasePartitionCommand extends Command
{
    // information schema table name.
    private const INFORMATION_SCHEMA_PARTITIONS_TABLE_NAME = 'INFORMATION_SCHEMA.PARTITIONS';

    // record offset (1 record)
    private const PRTITION_OFFSET_VALUE = 1;

    // partition setting key
    private const PRTITION_SETTING_KEY_DATABASE_NAME = 'databaseName';
    private const PRTITION_SETTING_KEY_TABLE_NAME = 'tableName';
    private const PRTITION_SETTING_KEY_PARTITION_TYPE = 'partitionYype';

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
        self:: PARTITION_TYPE_ID,
        self::PARTITION_TYPE_DATE
    ];

    /**
     * The name and signature of the console command.(コンソールコマンドの名前と使い方)
     *
     * @var string
     */
    protected $signature = 'admins:db-check-partition'; // if require parameter 'debug:test {param}';

    /**
     * The console command description.(コンソールコマンドの説明)
     *
     * @var string
     */
    protected $description = 'check database paprtition';


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
        // 現在日時(タイムゾーン付き)
        echo date('c') . "\n";
        echo TimeLibrary::getCurrentDateTime() . "\n";
        echo TimeLibrary::addMounths(TimeLibrary::getCurrentDateTime(), 3, TimeLibrary::DEFAULT_DATE_TIME_FORMAT_DATE_ONLY) . "\n";

        $this->check();
    }

    /**
     * get query builder by user id
     *
     * @return Builder
     */
    public function getQueryBuilderForInformantionSchema(): Builder
    {
        return DB::connection(BaseLogDataModel::setConnectionName())->table(self::INFORMATION_SCHEMA_PARTITIONS_TABLE_NAME);
    }

    /**
     * check partition.
     *
     * @return void
     * @throws MyApplicationHttpException
     */
    public function check(): void
    {
        $connection = BaseLogDataModel::setConnectionName();
        $database = Config::get("database.connections.${connection}.database");

        // テーブルごとのパーティション設定
        $partitionSettings = [
            [
                self::PRTITION_SETTING_KEY_DATABASE_NAME              => $database,
                self::PRTITION_SETTING_KEY_TABLE_NAME                 => (new AdminsLog())->getTable(),
                self::PRTITION_SETTING_KEY_PARTITION_TYPE             => self::PARTITION_TYPE_ID,
                self::ID_PRTITION_SETTING_KEY_TARGET_ID               => 1,
                self::ID_PRTITION_SETTING_KEY_BASE_NUMBER             => 100000,
                self::ID_PRTITION_SETTING_KEY_PARTITION_COUNT         => 10,
                self::NAME_PRTITION_SETTING_KEY_TARGET_DATE           => null,
                self::NAME_PRTITION_SETTING_KEY_PARTITION_MONTH_COUNT => null,
            ],
            [
                self::PRTITION_SETTING_KEY_DATABASE_NAME              => $database,
                self::PRTITION_SETTING_KEY_TABLE_NAME                 => (new UserCoinPaymentLog())->getTable(),
                self::PRTITION_SETTING_KEY_PARTITION_TYPE             => self::PARTITION_TYPE_DATE,
                self::ID_PRTITION_SETTING_KEY_TARGET_ID               => null,
                self::ID_PRTITION_SETTING_KEY_BASE_NUMBER             => null,
                self::ID_PRTITION_SETTING_KEY_PARTITION_COUNT         => null,
                self::NAME_PRTITION_SETTING_KEY_TARGET_DATE           => TimeLibrary::getCurrentDateTime(),
                self::NAME_PRTITION_SETTING_KEY_PARTITION_MONTH_COUNT => 3,
            ],
        ];

        // $value = $this->checkPartition((new UserCoinPaymentLog())->getTable());

        // echo var_dump($value);

        foreach($partitionSettings as $setting) {

            $latestPartition = $this->checkPartition($setting[self::PRTITION_SETTING_KEY_TABLE_NAME]);
            echo var_dump($latestPartition['PARTITION_NAME']);
            echo var_dump($latestPartition['PARTITION_ORDINAL_POSITION']);
            echo var_dump($latestPartition['TABLE_ROWS']);
            echo var_dump($latestPartition);


            if ($setting[self::PRTITION_SETTING_KEY_PARTITION_TYPE] === self::PARTITION_TYPE_ID) {
                // idでパーティションを貼る場合

                // パーティションが既に貼られている場合は次の範囲のIDでパーティションを設定する。
                if (!empty($latestPartition['PARTITION_ORDINAL_POSITION'])) {
                    // パーティション名から「p」の文字を切り取りIDを取得
                    $latestPartitionStartId = (int)mb_substr($latestPartition['PARTITION_NAME'], 1);
                    $nextPartitionStartId = $latestPartitionStartId + $setting[self::ID_PRTITION_SETTING_KEY_BASE_NUMBER];
                    $setting[self::ID_PRTITION_SETTING_KEY_TARGET_ID] = $nextPartitionStartId;
                    echo mb_substr($latestPartition['PARTITION_NAME'], 1) . "\n";
                    echo $nextPartitionStartId . "\n";

                    $this->addPartitionById(
                        $setting[self::PRTITION_SETTING_KEY_DATABASE_NAME],
                        $setting[self::PRTITION_SETTING_KEY_TABLE_NAME],
                        $setting[self::ID_PRTITION_SETTING_KEY_TARGET_ID],
                        $setting[self::ID_PRTITION_SETTING_KEY_BASE_NUMBER],
                        $setting[self::ID_PRTITION_SETTING_KEY_PARTITION_COUNT]
                    );
                } else {
                    $this->createPartitionById(
                        $setting[self::PRTITION_SETTING_KEY_DATABASE_NAME],
                        $setting[self::PRTITION_SETTING_KEY_TABLE_NAME],
                        $setting[self::ID_PRTITION_SETTING_KEY_TARGET_ID],
                        $setting[self::ID_PRTITION_SETTING_KEY_BASE_NUMBER],
                        $setting[self::ID_PRTITION_SETTING_KEY_PARTITION_COUNT]
                    );
                }
            } else if ($setting[self::PRTITION_SETTING_KEY_PARTITION_TYPE] === self::PARTITION_TYPE_DATE) {
                // 作成日時でパーティションを貼る場合
                $this->createPartitionByDate(
                    $setting[self::PRTITION_SETTING_KEY_DATABASE_NAME],
                     $setting[self::PRTITION_SETTING_KEY_TABLE_NAME],
                     $setting[self::NAME_PRTITION_SETTING_KEY_TARGET_DATE],
                     $setting[self::NAME_PRTITION_SETTING_KEY_PARTITION_MONTH_COUNT],
                    );
            } else {
                continue;
            }
        }
    }

    /**
     * check current partiion record
     *
     * @param string $tableName table name
     * @return array
     */
    public function checkPartition(string $tableName): array
    {
        // パーティションの情報の取得(最新の1件)
        $collection = $this->getQueryBuilderForInformantionSchema()
        // ->select(DB::raw('count(*) as user_count, status'))
        ->select(DB::raw("
            TABLE_SCHEMA,
            TABLE_NAME,
            PARTITION_NAME,
            PARTITION_ORDINAL_POSITION,
            TABLE_ROWS
        "))
        ->where('TABLE_NAME', '=', $tableName)
        // ->orderBy('PARTITION_NAME', 'desc') // 正しく最新のレコードを取れない
        ->orderBy('PARTITION_ORDINAL_POSITION', 'desc')
        ->limit(self::PRTITION_OFFSET_VALUE)
        ->get()
        ->toArray();

        return json_decode(json_encode($collection), true)[0];
    }

    /**
     * create partition by id.
     *
     * @param string $databaseName database name
     * @param string $tableName table name
     * @param string $id partition start id
     * @param string $baseNumber data count in 1 partition
     * @param string $count partition count
     * @return array
     */
    public function createPartitionById(
        string $databaseName,
        string $tableName,
        int $id = 1,
        int $baseNumber = 100000,
        int $count = 10
    ): void {
        // デフォルトは10万件ずつパーティションを分ける

        $partitions = '';

        // 追加する分のパーティション設定を作成
        foreach (range(0, $count) as $i) {
            $target = ($i * $baseNumber) + $id;
            $next = $baseNumber * ($i + $id);

            $partitionSetting = "PARTITION p${target} VALUES LESS THAN (${next})" . ($i <= ($count - 1) ? ', ' : '');
            $partitions .= $partitionSetting;
        }

        // echo var_dump($partitions);

        // パーティションの情報の作成
        DB::statement(
            "
                ALTER TABLE ${databaseName}.${tableName}
                PARTITION BY RANGE COLUMNS(id) (
                    ${partitions}
                )
            "
        );
    }

    /**
     * add partition by id.
     *
     * @param string $databaseName database name
     * @param string $tableName table name
     * @param string $id partition start id
     * @param string $baseNumber data count in 1 partition
     * @param string $count partition count
     * @return array
     */
    public function addPartitionById(
        string $databaseName,
        string $tableName,
        int $id = 1,
        int $baseNumber = 100000,
        int $count = 10
    ): void {
        // デフォルトは10万件ずつパーティションを分ける

        $partitions = '';

        // 追加する分のパーティション設定を作成
        foreach (range(0, $count) as $i) {
            $target = ($i * $baseNumber) + $id;
            $next = $target + $baseNumber;

            $partitionSetting = "PARTITION p${target} VALUES LESS THAN (${next})" . ($i <= ($count - 1) ? ', ' : '');
            $partitions .= $partitionSetting;
        }

        // パーティションの情報の追加
        DB::statement(
            "
                ALTER TABLE ${databaseName}.${tableName}
                ADD PARTITION (
                    ${partitions}
                )
            "
        );
    }

    /**
     * create partition by datetime.
     *
     * @param string $databaseName database name
     * @param string $tableName table name
     * @param string $currentDate partition start date time
     * @param int $mounthCount add partition count as month
     * @return array
     */
    public function createPartitionByDate(string $databaseName, string $tableName, string $currentDate, int $mounthCount = 3): void
    {
        $targetDate = TimeLibrary::addMounths($currentDate, $mounthCount);

        // パーティションの追加日数の算出
        $days = TimeLibrary::diffDays($currentDate, $targetDate);

        $partitions = '';

        // 追加する分のパーティション設定を作成
        foreach (range(0, $days) as $i) {
            $target = TimeLibrary::addDays($currentDate, $i, TimeLibrary::DATE_TIME_FORMAT_YMD);
            $next = TimeLibrary::addDays($currentDate, $i + 1, TimeLibrary::DEFAULT_DATE_TIME_FORMAT_DATE_ONLY);

            $partitionSetting = "PARTITION p${target} VALUES LESS THAN ('${next} 00:00:00')" . ($i <= ($days - 1) ? ', ' : '');
            $partitions .= $partitionSetting;
        }

        // echo var_dump($partitions);


        // パーティションの情報の作成
        DB::statement(
            "
                ALTER TABLE ${databaseName}.${tableName}
                PARTITION BY RANGE COLUMNS(created_at) (
                    ${partitions}
                )
            "
        );
    }

    /**
     * delete partiion.
     *
     * @param string $databaseName database name
     * @param string $tableName table name
     * @param string $partitionName partition name
     * @param int $mounthCount add partition count as month
     * @return array
     */
    public function deletePartition(string $databaseName, string $tableName, string $partitionName): void
    {
        DB::statement(
            "
                ALTER TABLE ${databaseName}.${tableName} DROP PARTITION ${partitionName};
            "
        );
    }
}
