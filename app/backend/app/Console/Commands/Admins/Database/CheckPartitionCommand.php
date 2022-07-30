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
use App\Models\Logs\UserCoinPaymentLog;
use App\Library\Time\TimeLibrary;


class CheckPartitionCommand extends Command
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
        // 現在日時(タイムゾーン付き)
        echo date('c') . "\n";

        echo TimeLibrary::getCurrentDateTime() . "\n";
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
        return DB::connection(UserCoinPaymentLog::setConnectionName())->table(self::INFORMATION_SCHEMA_PARTITIONS_TABLE_NAME);
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
}
