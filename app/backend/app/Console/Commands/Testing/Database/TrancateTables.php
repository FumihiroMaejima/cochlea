<?php

namespace App\Console\Commands\Testing\Database;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Library\Time\TimeLibrary;

class TrancateTables extends Command
{
    /** @var string CONNECTION_NAME_FOR_CI CIなどで使う場合のコネクション名。単一のコネクションに接続させる。 */
    private const CONNECTION_NAME_FOR_CI = 'sqlite';
    /** @var string CONNECTION_NAME_FOR_TESTING UnitTestで使う場合のコネクション名。単一のコネクションに接続させる。 */
    private const CONNECTION_NAME_FOR_TESTING = 'mysql_testing';

    private const PARAMETER_KEY_TABLES = 'tables';

    /**
     * The name and signature of the console command.(コンソールコマンドの名前と使い方)
     *
     * @var string
     */
    protected $signature = 'testing:truncate {tables*}';

    /**
     * The console command description.(コンソールコマンドの説明)
     *
     * @var string
     */
    protected $description = 'debug test command with parameter.';


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
        $values = $this->arguments();

        // 現在日時(タイムゾーン付き)
        echo date('c') . "\n";
        echo 'Truncate Tables.' . "\n";
        // echo var_dump($values) . "\n";
        if (config('app.env') === 'testing') {
            // artisanコマンド自体のパラメーターも含まれる為、keyを指定する。
            $tables = $values[self::PARAMETER_KEY_TABLES];
            if (!empty($tables)) {
                self::truncateTable($values[self::PARAMETER_KEY_TABLES]);
            }
        }
    }

    /**
     * truncate some tables.
     *
     * @param array $tables table name
     * @param int $mounthCount add partition count as month
     * @return void
     */
    private static function truncateTable(array $tables): void
    {
        // 対象のDBの設定
        $logsConnectionName = Config::get('myapp.database.logs.baseConnectionName');
        $userConnectionName = Config::get('myapp.database.users.baseConnectionName');
        $connection = '';

        // connection 設定がCI用の設定の場合
        if (($logsConnectionName === self::CONNECTION_NAME_FOR_CI) && ($userConnectionName === self::CONNECTION_NAME_FOR_CI)) {
            $connection = self::CONNECTION_NAME_FOR_CI;
        } else {
            // テスト用DB内のテーブル
            $connection = self::CONNECTION_NAME_FOR_TESTING;
        }
        $database = Config::get("database.connections.${connection}.database");

        // TRUNCATEの実行
        foreach ($tables as $table) {
            // IF EXISTS
            /* DB::statement(
                "
                    SELECT * FROM ${database}.${table};
                "
            ); */
            DB::statement(
                "
                    TRUNCATE TABLE ${database}.${table};
                "
            );
        }
    }
}
