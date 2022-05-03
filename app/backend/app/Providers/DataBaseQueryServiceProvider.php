<?php

namespace App\Providers;

use Illuminate\Database\Events\TransactionBeginning;
use Illuminate\Database\Events\TransactionCommitted;
use Illuminate\Database\Events\TransactionRolledBack;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use DateTime;

class DataBaseQueryServiceProvider extends ServiceProvider
{
    private const LOG_CAHNNEL_NAME = 'sqllog';
    private const LOG_TRANSACTION_START_MESSAGE = 'START TRANSACTION';
    private const LOG_TRANSACTION_COMMIT_MESSAGE = 'TRANSACTION COMMIT';
    private const LOG_TRANSACTION_ROLLBACK_MESSAGE = 'TRANSACTION ROLLBACK';

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $env = Config::get('app.env');
        if ($env === 'production' || $env === 'testing') {
            return;
        }

        DB::listen(function ($query): void {
            $sql = $query->sql;

            foreach ($query->bindings as $binding) {
                if (is_string($binding)) {
                    $binding = "'{$binding}'";
                } elseif (is_bool($binding)) {
                    $binding = $binding ? '1' : '0';
                } elseif (is_int($binding)) {
                    $binding = (string) $binding;
                } elseif ($binding === null) {
                    $binding = 'NULL';
                } elseif ($binding instanceof Carbon) {
                    $binding = "'{$binding->toDateTimeString()}'";
                } elseif ($binding instanceof DateTime) {
                    $binding = "'{$binding->format('Y-m-d H:i:s')}'";
                }

                $sql = preg_replace('/\\?/', $binding, $sql, 1);
            }

           //  Log::debug('SQL', ['sql' => $sql, 'time' => "{$query->time} ms"]);
            Log::channel(self::LOG_CAHNNEL_NAME)->info('SQL', ['sql' => $sql, 'time' => "{$query->time} ms"]);

            Event::listen(TransactionBeginning::class, function (TransactionBeginning $event): void {
                Log::debug(self::LOG_TRANSACTION_START_MESSAGE);
                Log::channel(self::LOG_CAHNNEL_NAME)->info('SQL: ' . self::LOG_TRANSACTION_START_MESSAGE);
            });

            Event::listen(TransactionCommitted::class, function (TransactionCommitted $event): void {
                Log::debug(self::LOG_TRANSACTION_COMMIT_MESSAGE);
                Log::channel(self::LOG_CAHNNEL_NAME)->info('SQL: ' . self::LOG_TRANSACTION_COMMIT_MESSAGE);
            });

            Event::listen(TransactionRolledBack::class, function (TransactionRolledBack $event): void {
                Log::debug(self::LOG_TRANSACTION_ROLLBACK_MESSAGE);
                Log::channel(self::LOG_CAHNNEL_NAME)->info('SQL: ' . self::LOG_TRANSACTION_ROLLBACK_MESSAGE);
            });
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
