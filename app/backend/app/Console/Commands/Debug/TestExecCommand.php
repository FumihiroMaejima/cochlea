<?php

declare(strict_types=1);

namespace App\Console\Commands\Debug;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Library\Time\TimeLibrary;

class TestExecCommand extends Command
{
    /**
     * The name and signature of the console command.(コンソールコマンドの名前と使い方)
     *
     * @var string
     */
    protected $signature = 'debug:exec'; // if require parameter 'debug:test {param}';

    /**
     * The console command description.(コンソールコマンドの説明)
     *
     * @var string
     */
    protected $description = 'debug exec command';


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

        $output = '';
        $dabase = 'database_name';

        // 読み込みモードでファイルを開く
        $fp = fopen("./storage/app/sql/sample.sql", "r");

        if ($fp) {
            // ファイルを1行ずつ取得する
            while ($line = fgets($fp)) {
                if (preg_match('/INSERT INTO/', $line)) {
                    # DB名を指定
                    $output .= str_replace('INSERT INTO ', "INSERT INTO $dabase.", $line);
                } elseif (!preg_match("/use $dabase/", $line) && !preg_match('/-- /', $line)) {
                    // use宣言とコメントを省略
                    $output .= $line;
                }
            }

            // ファイルを閉じる
            fclose($fp);

            $sql = $output;

            echo $sql; // SQLの出力

            // SQLの実行
            DB::statement(
                $sql
            );
        }

        echo TimeLibrary::getCurrentDateTime() . "\n";
    }
}
