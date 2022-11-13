<?php

namespace App\Console\Commands\Admins\Database;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Library\Database\DatabaseLibrary;
use App\Library\Database\PartitionLibrary;
use App\Library\Database\ShardingLibrary;
use App\Models\Logs\AdminsLog;
use App\Models\Logs\BaseLogDataModel;
use App\Models\Logs\UserCoinPaymentLog;
use App\Models\Logs\UserReadInformationLog;
use App\Models\Users\UserCoinHistories;
use App\Models\Users\UserCoinPaymentStatus;
use App\Models\Users\UserReadInformations;
use App\Library\Time\TimeLibrary;

class BaseDatabasePartitionsCommand extends Command
{
    // information schema table name.
    private const INFORMATION_SCHEMA_PARTITIONS_TABLE_NAME = 'INFORMATION_SCHEMA.PARTITIONS';

    // record offset (1 record)
    private const PRTITION_OFFSET_VALUE = 1;

    // partition setting key
    protected const PRTITION_SETTING_KEY_CONNECTION_NAME = 'databaseName';
    protected const PRTITION_SETTING_KEY_TABLE_NAME = 'tableName';
    protected const PRTITION_SETTING_KEY_PARTITION_TYPE = 'partitionYype';
    protected const PRTITION_SETTING_KEY_COLUMN_NAME = 'columnName';

    // パーティションタイプごとの詳細な設定
    protected const ID_PRTITION_SETTING_KEY_TARGET_ID = 'targetId'; // パーティション数の起算ID
    protected const ID_PRTITION_SETTING_KEY_BASE_NUMBER = 'baseNumber'; // 1パーティションあたりのID数
    protected const ID_PRTITION_SETTING_KEY_PARTITION_COUNT = 'partitionCount'; // パーティション数
    protected const NAME_PRTITION_SETTING_KEY_TARGET_DATE = 'targetDate'; // パーティション数の起算日
    protected const NAME_PRTITION_SETTING_KEY_PARTITION_MONTH_COUNT = 'monthCount'; // パーティション数(1パーティション=1日を月数で設定)
    protected const NAME_PRTITION_SETTING_KEY_STORE_MONTH_COUNT = 'storeMonthCount'; // パーティションの保存月数(削除しない場合は未指定。)
    // partition type
    protected const PARTITION_TYPE_ID = 1;
    protected const PARTITION_TYPE_DATE = 2;
    protected const PARTITION_TYPE_HASH_ID = 3;

    public const PARTITION_TYPES = [
        self::PARTITION_TYPE_ID,
        self::PARTITION_TYPE_DATE,
        self::PARTITION_TYPE_HASH_ID,
    ];

    protected const ALTER_TABLE_TYPE_CREATE = 'create';
    protected const ALTER_TABLE_TYPE_ADD = 'add';

    protected const ALTER_TABLE_TYPES = [
        self::ALTER_TABLE_TYPE_CREATE,
        self::ALTER_TABLE_TYPE_ADD
    ];

    /**
     * The name and signature of the console command.(コンソールコマンドの名前と使い方)
     *
     * @var string
     */
    protected $signature = 'admins:add-debug-partitions'; // if require parameter 'debug:test {param}';

    /**
     * The console command description.(コンソールコマンドの説明)
     *
     * @var string
     */
    protected $description = 'add database paprtitions';


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
        $this->setPartitions();
    }

    /**
     * get settings for adding partition target tables.
     *
     * @return array
     */
    protected function getPartitionSettings(): array
    {
        $connection = 'testconnection';

        // テーブルごとのパーティション設定
        return [
            [
                self::PRTITION_SETTING_KEY_CONNECTION_NAME            => $connection,
                self::PRTITION_SETTING_KEY_TABLE_NAME                 => 'table name',
                self::PRTITION_SETTING_KEY_PARTITION_TYPE             => self::PARTITION_TYPE_ID,
                self::PRTITION_SETTING_KEY_COLUMN_NAME                => 'column name',
                self::ID_PRTITION_SETTING_KEY_TARGET_ID               => 1, // IDカラムを元にパーティションを貼る場合は必要
                self::ID_PRTITION_SETTING_KEY_BASE_NUMBER             => 100000, // IDカラムを元にパーティションを貼る場合は必要
                self::ID_PRTITION_SETTING_KEY_PARTITION_COUNT         => 10, // IDカラムを元にパーティションを貼る場合は必要
                self::NAME_PRTITION_SETTING_KEY_TARGET_DATE           => null, // IDカラムを元にパーティションを貼る場合は不要
                self::NAME_PRTITION_SETTING_KEY_PARTITION_MONTH_COUNT => null, // IDカラムを元にパーティションを貼る場合は不要
                self::NAME_PRTITION_SETTING_KEY_STORE_MONTH_COUNT     => null, // IDカラムを元にパーティションを貼る場合は不要
            ],
            [
                self::PRTITION_SETTING_KEY_CONNECTION_NAME            => $connection,
                self::PRTITION_SETTING_KEY_TABLE_NAME                 => 'table name',
                self::PRTITION_SETTING_KEY_PARTITION_TYPE             => self::PARTITION_TYPE_DATE,
                self::PRTITION_SETTING_KEY_COLUMN_NAME                => 'column name',
                self::ID_PRTITION_SETTING_KEY_TARGET_ID               => null, // 日時カラムを元にパーティションを貼る場合は不要
                self::ID_PRTITION_SETTING_KEY_BASE_NUMBER             => null, // 日時カラムを元にパーティションを貼る場合は不要
                self::ID_PRTITION_SETTING_KEY_PARTITION_COUNT         => null, // 日時カラムを元にパーティションを貼る場合は不要
                self::NAME_PRTITION_SETTING_KEY_TARGET_DATE           => TimeLibrary::getCurrentDateTime(), // 日時カラムを元にパーティションを貼る場合は必要
                self::NAME_PRTITION_SETTING_KEY_PARTITION_MONTH_COUNT => 3, // 日時カラムを元にパーティションを貼る場合は必要
                self::NAME_PRTITION_SETTING_KEY_STORE_MONTH_COUNT     => 4, // 日時カラムを元にパーティションを貼る、かつ定期的に削除したい場合は必要
            ],
        ];
    }

    /**
     * get query builder by user id
     *
     * @param string $connection connection name
     * @return Builder
     */
    private function getQueryBuilderForInformantionSchema(string $connection): Builder
    {
        return DB::connection($connection)->table(self::INFORMATION_SCHEMA_PARTITIONS_TABLE_NAME);
    }

    /**
     * set partitions.
     *
     * @return void
     * @throws MyApplicationHttpException
     */
    protected function setPartitions(): void
    {
        // パーティションを設定する対象のテーブル情報の取得
        $partitionSettings = $this->getPartitionSettings();

        foreach ($partitionSettings as $setting) {
            $latestPartition = $this->checkLatestPartition(
                $setting[self::PRTITION_SETTING_KEY_CONNECTION_NAME],
                $setting[self::PRTITION_SETTING_KEY_TABLE_NAME]
            );
            $alterTableType = self::ALTER_TABLE_TYPE_CREATE;

            if ($setting[self::PRTITION_SETTING_KEY_PARTITION_TYPE] === self::PARTITION_TYPE_ID) {
                // idでパーティションを貼る場合

                // パーティションが既に貼られている場合は次の範囲のIDでパーティションを設定する。
                if (!empty($latestPartition['PARTITION_ORDINAL_POSITION'])) {
                    // パーティション名から「p」の文字を切り取りIDを取得
                    $latestPartitionStartId = (int)mb_substr($latestPartition['PARTITION_NAME'], 1);
                    $nextPartitionStartId = $latestPartitionStartId + $setting[self::ID_PRTITION_SETTING_KEY_BASE_NUMBER];
                    $setting[self::ID_PRTITION_SETTING_KEY_TARGET_ID] = $nextPartitionStartId;
                    $alterTableType = self::ALTER_TABLE_TYPE_ADD;
                }

                $this->addPartitionById(
                    $setting[self::PRTITION_SETTING_KEY_CONNECTION_NAME],
                    $setting[self::PRTITION_SETTING_KEY_TABLE_NAME],
                    $setting[self::PRTITION_SETTING_KEY_COLUMN_NAME],
                    $setting[self::ID_PRTITION_SETTING_KEY_TARGET_ID],
                    $setting[self::ID_PRTITION_SETTING_KEY_BASE_NUMBER],
                    $setting[self::ID_PRTITION_SETTING_KEY_PARTITION_COUNT],
                    $alterTableType
                );
            } elseif ($setting[self::PRTITION_SETTING_KEY_PARTITION_TYPE] === self::PARTITION_TYPE_DATE) {
                // 作成日時でパーティションを貼る場合

                // パーティションが既に貼られている場合は最新の日付の翌日の日付でパーティションを設定する。
                if (!empty($latestPartition['PARTITION_ORDINAL_POSITION'])) {
                    // パーティション名から「p」の文字を切り取り日付を取得
                    $latestPartitionDate = mb_substr($latestPartition['PARTITION_NAME'], 1);
                    $nextPartitionDate = TimeLibrary::addDays($latestPartitionDate, 1);
                    $setting[self::NAME_PRTITION_SETTING_KEY_TARGET_DATE] = $nextPartitionDate;
                    $alterTableType = self::ALTER_TABLE_TYPE_ADD;
                }

                $this->addPartitionByDate(
                    $setting[self::PRTITION_SETTING_KEY_CONNECTION_NAME],
                    $setting[self::PRTITION_SETTING_KEY_TABLE_NAME],
                    $setting[self::PRTITION_SETTING_KEY_COLUMN_NAME],
                    $setting[self::NAME_PRTITION_SETTING_KEY_TARGET_DATE],
                    $setting[self::NAME_PRTITION_SETTING_KEY_PARTITION_MONTH_COUNT],
                    $alterTableType
                );
            } elseif ($setting[self::PRTITION_SETTING_KEY_PARTITION_TYPE] === self::PARTITION_TYPE_HASH_ID) {
                // user_idのHASHでパーティションを貼る場合

                $paritionCount = $setting[self::ID_PRTITION_SETTING_KEY_PARTITION_COUNT];
                $latestPartitionPosition = 0;

                // パーティションが既に貼られている場合はを設定値に達するまでパーテションを追加
                if (!empty($latestPartition['PARTITION_ORDINAL_POSITION'])) {
                    // hashはPARTITION_ORDINAL_POSITIONを参照する
                    $latestPartitionPosition = (int)$latestPartition['PARTITION_ORDINAL_POSITION'];

                    // 設定以上のパーティションを作る必要が無い為skip
                    if (($latestPartitionPosition >= $setting[self::ID_PRTITION_SETTING_KEY_PARTITION_COUNT]) ||
                        (($setting[self::ID_PRTITION_SETTING_KEY_PARTITION_COUNT] - $latestPartitionPosition) <= 0)
                    ) {
                        continue;
                    } else {
                        $alterTableType = self::ALTER_TABLE_TYPE_ADD;
                        $paritionCount = $setting[self::ID_PRTITION_SETTING_KEY_PARTITION_COUNT] - $latestPartitionPosition;
                    }
                }

                $this->addHashPartitionById(
                    $setting[self::PRTITION_SETTING_KEY_CONNECTION_NAME],
                    $setting[self::PRTITION_SETTING_KEY_TABLE_NAME],
                    $setting[self::PRTITION_SETTING_KEY_COLUMN_NAME],
                    $setting[self::ID_PRTITION_SETTING_KEY_BASE_NUMBER],
                    $paritionCount,
                    $latestPartitionPosition,
                    $alterTableType
                );
            } else {
                continue;
            }
        }
    }

    /**
     * remove partitions.
     *
     * @return void
     * @throws MyApplicationHttpException
     */
    protected function removePartitions(): void
    {
        // パーティションを設定する対象のテーブル情報の取得
        $partitionSettings = $this->getPartitionSettings();

        foreach ($partitionSettings as $setting) {
            // 日付でパーティションを作成していない場合や保存期間を設定していない(=永続化する)場合
            if (($setting[self::PRTITION_SETTING_KEY_PARTITION_TYPE] !== self::PARTITION_TYPE_DATE)
                || (is_null($setting[self::NAME_PRTITION_SETTING_KEY_STORE_MONTH_COUNT]))
            ) {
                continue;
            }

            // 1週間前より前の日付のパーティションは削除
            // $dateTime = TimeLibrary::subDays(TimeLibrary::getCurrentDateTime(), 5, TimeLibrary::DATE_TIME_FORMAT_YMD);

            // 現在から設定された保存期間を引いたの日付を設定
            $dateTime = TimeLibrary::subMonths(
                TimeLibrary::getCurrentDateTime(),
                $setting[self::NAME_PRTITION_SETTING_KEY_STORE_MONTH_COUNT],
                TimeLibrary::DATE_TIME_FORMAT_YMD
            );

            $partions = $this->getPartitionsByTableName(
                $setting[self::PRTITION_SETTING_KEY_CONNECTION_NAME],
                $setting[self::PRTITION_SETTING_KEY_TABLE_NAME]
            );

            // 保存期間が切れたパーティションを取得
            $expiredPartions = self::filteringPartitionsByDateTime($partions, $dateTime);

            // TODO　delete paririonの実行
            echo var_dump($expiredPartions);
        }
    }

    /**
     * add partition by id.
     *
     * @param string $connection connection name
     * @param string $tableName table name
     * @param string $columnName column name
     * @param string $id partition start id
     * @param string $baseNumber data count in 1 partition
     * @param string $count partition count
     * @param string $type alter table type
     * @return array
     */
    private function addPartitionById(
        string $connection,
        string $tableName,
        string $columnName,
        int $id = 1,
        int $baseNumber = 100000,
        int $count = 10,
        string $type = self::ALTER_TABLE_TYPE_CREATE
    ): void {
        // デフォルトは10万件ずつパーティションを分ける

        // TODO IDの最大値の確認

        // typeの値の確認
        if (!in_array($type, self::ALTER_TABLE_TYPES)) {
            return;
        }

        $partitions = '';

        // 追加する分のパーティション設定を作成
        foreach (range(0, $count) as $i) {
            $target = $i !== 0 ? (($i * $baseNumber) + $id) : $id;
            $next = ($target + $baseNumber - 1);

            $partitionSetting = "PARTITION p${target} VALUES LESS THAN (${next})" . ($i <= ($count - 1) ? ', ' : '');
            $partitions .= $partitionSetting;
        }

        $databaseName = DatabaseLibrary::getDatabaseNameByConnection($connection);

        // パーティションの情報の追加
        if ($type === self::ALTER_TABLE_TYPE_CREATE) {
            // 新規作成(上書き)
            PartitionLibrary::createPartitionsByRange($databaseName, $tableName, $columnName, $partitions);
        } else {
            // 追加
            PartitionLibrary::addPartitions($databaseName, $tableName, $partitions);
        }
    }

    /**
     * add partition by datetime.
     *
     * @param string $connection connection name
     * @param string $tableName table name
     * @param string $columnName column name
     * @param string $currentDate partition start date time
     * @param int $monthCount add partition count as month
     * @param string $type alter table type
     * @return array
     */
    private function addPartitionByDate(
        string $connection,
        string $tableName,
        string $columnName,
        string $currentDate,
        int $monthCount = 3,
        string $type = self::ALTER_TABLE_TYPE_CREATE
    ): void {
        // typeの値の確認
        if (!in_array($type, self::ALTER_TABLE_TYPES)) {
            return;
        }

        $targetDate = TimeLibrary::addMonths($currentDate, $monthCount);

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

        $databaseName = DatabaseLibrary::getDatabaseNameByConnection($connection);

        // パーティションの情報の追加
        if ($type === self::ALTER_TABLE_TYPE_CREATE) {
            // 新規作成(上書き)
            PartitionLibrary::createPartitionsByRange($databaseName, $tableName, $columnName, $partitions);
        } else {
            // 追加
            PartitionLibrary::addPartitions($databaseName, $tableName, $partitions);
        }
    }


    /**
     * add hash partitions by id.
     *
     * @param string $connection connection name
     * @param string $tableName table name
     * @param string $columnName column name
     * @param string $baseNumber div base nubmer
     * @param int $count partition count
     * @param int $position partition start position
     * @param string $type alter table type
     * @return array
     */
    private function addHashPartitionById(
        string $connection,
        string $tableName,
        string $columnName,
        int $baseNumber = 16,
        int $count = 16,
        int $position = 1,
        string $type = self::ALTER_TABLE_TYPE_CREATE
    ): void {
        // typeの値の確認
        if (!in_array($type, self::ALTER_TABLE_TYPES)) {
            return;
        }

        $databaseName = DatabaseLibrary::getDatabaseNameByConnection($connection);

        // パーティションの情報の追加
        if ($type === self::ALTER_TABLE_TYPE_CREATE) {
            // 新規作成(上書き)
            PartitionLibrary::createPartitionsByHashDiv(
                $databaseName,
                $tableName,
                $columnName,
                $baseNumber,
                $count
            );
        } else {
            $partitions = '';
            // 追加する分のパーティション設定を作成
            foreach (range(1, $count) as $i) {
                $name = $position + $i;
                $partitionSetting = "PARTITION p${name}";
                $partitions .= $partitionSetting;
            }
            // 追加
            PartitionLibrary::addPartitions($databaseName, $tableName, $partitions);
        }
    }

    /**
     * check current partiion record
     *
     * @param string $connection connection name
     * @param string $tableName table name
     * @return array
     */
    private function checkLatestPartition(string $connection, string $tableName): array
    {
        $schema = DatabaseLibrary::getDatabaseNameByConnection($connection);

        // パーティションの情報の取得(最新の1件)
        // `PARTITION_NAME`では正しくソートされないので`PARTITION_ORDINAL_POSITION`でソートをかける
        $collection = $this->getQueryBuilderForInformantionSchema($connection)
            ->select(DB::raw("
                TABLE_SCHEMA,
                TABLE_NAME,
                PARTITION_NAME,
                PARTITION_ORDINAL_POSITION,
                TABLE_ROWS,
                CREATE_TIME
            "))
            ->where('TABLE_SCHEMA', '=', $schema)
            ->where('TABLE_NAME', '=', $tableName)
            ->orderBy('PARTITION_ORDINAL_POSITION', 'desc')
            ->limit(self::PRTITION_OFFSET_VALUE)
            ->get()
            ->toArray();

        if (empty($collection)) {
            return [];
        }

        return json_decode(json_encode($collection), true)[0];
    }


    /**
     * get partiion by table name
     *
     * @param string $connection connection name
     * @param string $tableName table name
     * @return array
     */
    private function getPartitionsByTableName(
        string $connection,
        string $tableName,
    ): array {
        $schema = DatabaseLibrary::getDatabaseNameByConnection($connection);

        // パーティションの情報の取得(指定された日付より以前のパーティション)
        // `PARTITION_NAME`では正しくソートされないので`PARTITION_ORDINAL_POSITION`でソートをかける
        // CREATE_TIMEはpartitionを追加する度に更新されている？っぽいのでwhereに不向きかも。
        // PARTITION_DESCRIPTIONの方が良さそう
        $collection = $this->getQueryBuilderForInformantionSchema($connection)
            ->select(DB::raw("
                TABLE_SCHEMA,
                TABLE_NAME,
                PARTITION_NAME,
                PARTITION_ORDINAL_POSITION,
                TABLE_ROWS,
                CREATE_TIME,
                PARTITION_DESCRIPTION
            "))
            ->where('TABLE_SCHEMA', '=', $schema)
            ->where('TABLE_NAME', '=', $tableName)
            ->orderBy('PARTITION_ORDINAL_POSITION', 'ASC')
            ->get()
            ->toArray();

        if (empty($collection)) {
            return [];
        }

        return json_decode(json_encode($collection), true);
    }


    /**
     * filtering partiions by datetime
     *
     * @param array $partitions partitions
     * @param string|null $dateTime target date time
     * @return array
     */
    private function filteringPartitionsByDateTime(
        array $partitions,
        string|null $dateTime = null
    ): array {
        if (is_null($dateTime)) {
            $dateTime = TimeLibrary::getCurrentDateTime(TimeLibrary::DATE_TIME_FORMAT_YMD);
        }

        $response = [];
        foreach ($partitions as $partition) {
            // パーティションが既に貼られている場合は最新の日付の翌日の日付でパーティションを設定する。
            if (empty($partition['PARTITION_ORDINAL_POSITION'])) {
                continue;
            }

            // パーティション名から「p」の文字を切り取り日付を取得
            $partitionDate = mb_substr($partition['PARTITION_NAME'], 1);

            // 指定された日付未満の場合
            if (TimeLibrary::lesser($partitionDate, $dateTime)) {
                $response[$partition['PARTITION_ORDINAL_POSITION']] = $partition;
            }
        }

        return $response;
    }


    /**
     * get log database settings for partition target tables.
     *
     * @return array
     */
    protected function getLogDatabasePartitionSettings(): array
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
                self::NAME_PRTITION_SETTING_KEY_STORE_MONTH_COUNT     => 4,
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
                self::NAME_PRTITION_SETTING_KEY_STORE_MONTH_COUNT     => 4,
            ],
            [
                self::PRTITION_SETTING_KEY_CONNECTION_NAME            => $connection,
                self::PRTITION_SETTING_KEY_TABLE_NAME                 => (new UserReadInformationLog())->getTable(),
                self::PRTITION_SETTING_KEY_PARTITION_TYPE             => self::PARTITION_TYPE_DATE,
                self::PRTITION_SETTING_KEY_COLUMN_NAME                => UserCoinPaymentLog::CREATED_AT,
                self::ID_PRTITION_SETTING_KEY_TARGET_ID               => null,
                self::ID_PRTITION_SETTING_KEY_BASE_NUMBER             => null,
                self::ID_PRTITION_SETTING_KEY_PARTITION_COUNT         => null,
                self::NAME_PRTITION_SETTING_KEY_TARGET_DATE           => TimeLibrary::getCurrentDateTime(),
                self::NAME_PRTITION_SETTING_KEY_PARTITION_MONTH_COUNT => 3,
                self::NAME_PRTITION_SETTING_KEY_STORE_MONTH_COUNT     => 4,
            ],
        ];
    }


    /**
     * get user database settings for partition target tables.
     *
     * @return array
     */
    protected function getUserDatabsePartitionSettings(): array
    {
        $partitionSettings = [];
        $dateTime = TimeLibrary::getCurrentDateTime();

        // テーブルごとのパーティション設定
        foreach (ShardingLibrary::getShardingSetting() as $node => $shardIds) {
            $connection = ShardingLibrary::getConnectionByNodeNumber($node);

            foreach ($shardIds as $shardId) {
                $partitionSettings = array_merge(
                    $partitionSettings,
                    [
                        [
                            self::PRTITION_SETTING_KEY_CONNECTION_NAME            => $connection,
                            self::PRTITION_SETTING_KEY_TABLE_NAME                 => (new UserCoinHistories())->getTable().$shardId,
                            self::PRTITION_SETTING_KEY_PARTITION_TYPE             => self::PARTITION_TYPE_HASH_ID,
                            self::PRTITION_SETTING_KEY_COLUMN_NAME                => UserCoinHistories::USER_ID,
                            self::ID_PRTITION_SETTING_KEY_TARGET_ID               => null,
                            self::ID_PRTITION_SETTING_KEY_BASE_NUMBER             => 16,
                            self::ID_PRTITION_SETTING_KEY_PARTITION_COUNT         => 16,
                            self::NAME_PRTITION_SETTING_KEY_TARGET_DATE           => null,
                            self::NAME_PRTITION_SETTING_KEY_PARTITION_MONTH_COUNT => null,
                            self::NAME_PRTITION_SETTING_KEY_STORE_MONTH_COUNT     => null,
                        ],
                        [
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
                        ],
                        [
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
                        ],
                    ]
                );
            }
        }

        return $partitionSettings;
    }
}
