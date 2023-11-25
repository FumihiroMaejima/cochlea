<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Throwable;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Admins\Coin\UserCoinHistoriesCountingCommand::class,
        Commands\Admins\Database\AddLogDatabasePartitionsCommand::class,
        Commands\Admins\Database\AddUserDatabasePartitionsCommand::class,
        Commands\Admins\Database\RemoveLogDatabasePartitionsCommand::class,
        Commands\Admins\Database\RemoveUserDatabasePartitionsCommand::class,
        Commands\Debug\TestCommand::class,
        Commands\Debug\TestCommandWithParam::class,
        Commands\Debug\TestExecCommand::class,
        Commands\Debug\Seeder\UserCoinHistoriesExpiredSeedCommand::class,
        Commands\Debug\Seeder\UserCoinHistoriesSeedCommand::class,
        Commands\Testing\Database\TrancateTables::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();

        // スケジュールを設定して定期実行させる場合
        // 下記の例は、9:00~23:00の間、1時間ごと毎時5分にタスク実行
        /* $schedule->command('debug:test')
        ->hourlyAt(5)
        ->between('9:00', '23:00'); */
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    /**
     * Report the exception to the exception handler.
     *
     * @param  \Throwable  $e
     * @return void
     */
    protected function reportException(Throwable $e)
    {
        // 実際に呼び出されるクラスは
        // NunoMaduro\Collision\Adapters\Laravel\ExceptionHandler
        $this->app[ExceptionHandler::class]->report($e);
        // parent::reportException($e);
    }

    /**
     * Render the given exception.
     *
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @param  \Throwable  $e
     * @return void
     */
    protected function renderException($output, Throwable $e)
    {
        $this->app[ExceptionHandler::class]->renderForConsole($output, $e);
    }
}
