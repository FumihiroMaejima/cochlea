<?php

namespace App\Console\Commands\Testing\Database;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Library\Database\ShardingLibrary;
use App\Library\Time\TimeLibrary;
use Exception;

class TrancateTables extends Command
{
    private const PARAMETER_KEY_TABLES = 'tables';
    /** @var string CONNECTION_NAME_FOR_CI CIなどで使う場合のコネクション名。単一のコネクションに接続させる。 */
    private const CONNECTION_NAME_FOR_CI = 'sqlite';

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
        $connection = ShardingLibrary::getSingleConnectionByConfig();
        $database = Config::get("database.connections.${connection}.database");

        try {
            // TRUNCATEの実行
            foreach ($tables as $table) {

                if ($connection === self::CONNECTION_NAME_FOR_CI) {
                    // DBがsqliteの場合
                    DB::statement(
                        "
                            DELETE FROM ${database}.${table};
                            DELETE FROM sqlite_sequence WHERE name = ${database}.${table};
                        "
                    );

                } else {
                    DB::statement(
                        "
                            TRUNCATE TABLE ${database}.${table};
                        "
                    );
                }
                /* DB::statement(
                    "
                        TRUNCATE TABLE ${database}.${table};
                    "
                ); */
            }
        } catch (Exception $e) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_500,
                'テスト用バッチ実行エラー。データの初期化に失敗しました。 ' . TimeLibrary::getCurrentDateTime(),
                ['tables' => $tables],
                false,
                $e
            );
        }
    }
}
