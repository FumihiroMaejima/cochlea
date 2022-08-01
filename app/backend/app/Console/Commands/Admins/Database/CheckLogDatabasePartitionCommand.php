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
     * check
     *
     * @return mixed
     * @throws MyApplicationHttpException
     */
    public function check(): mixed
    {
        $value = $this->checkPartition();

        echo var_dump($value);

        $this->addPartition();

        return $value;
    }

    /**
     * get query builder by user id
     *
     * @return Builder
     */
    public function getQueryBuilder(): Builder
    {
        return DB::connection(UserCoinPaymentLog::setConnectionName())->table((new UserCoinPaymentLog())->getTable());
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
     * check current partiion record
     *
     * @return array
     */
    public function checkPartition(): array
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
        ->where('TABLE_NAME', '=', (new UserCoinPaymentLog())->getTable())
        ->orderBy('PARTITION_NAME', 'desc')
        ->limit(self::PRTITION_OFFSET_VALUE)
        ->get()
        ->toArray();

        return json_decode(json_encode($collection), true);

            /* $collection = $this->getQueryBuilder()
                ->selectRaw("
                    SELECT
                        TABLE_SCHEMA,
                        TABLE_NAME,
                        PARTITION_NAME,
                        PARTITION_ORDINAL_POSITION,
                        TABLE_ROWS
                    FROM INFORMATION_SCHEMA.PARTITIONS
                    WHERE TABLE_NAME='user_coin_payment_log'
                    ORDER BY PARTITION_NAME DESC
                    LIMIT 1
                    ;
                ")->get();
            */
    }

    /**
     * add partition.
     *
     * @return array
     */
    public function addPartition(): void
    {
        $table = (new UserCoinPaymentLog())->getTable();

        $currentDate = TimeLibrary::getCurrentDateTime();

        $targetDate = TimeLibrary::addMounths($currentDate, 3);

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

        echo var_dump($partitions);


        // パーティションの情報の取得(最新の1件)
        DB::statement(
            "
                ALTER TABLE cochlea_logs.${table}
                PARTITION BY RANGE COLUMNS(created_at) (
                    -- PARTITION p20220731 VALUES LESS THAN ('2022-08-01 00:00:00')
                    -- PARTITION p20220801 VALUES LESS THAN ('2022-08-02 00:00:00'),
                    -- PARTITION p20220802 VALUES LESS THAN ('2022-08-03 00:00:00')
                    ${partitions}
                )
            "
        );

    }
}
