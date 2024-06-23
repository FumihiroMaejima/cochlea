<?php

declare(strict_types=1);

namespace App\Library\Process;

use stdClass;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ProcessLibrary
{
    /**
     * sampel process.
     * @return string
     */
    public static function sampleProcess(): string
    {
        $process = new Process(['ls', 'app', '-a']);
        $process->run();
        // プロセスIDの取得
        $process->getPid();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }

    /**
     * sampel artisan command process.
     * @return void
     */
    public static function sampleArtisanCommandProcess(): void
    {
        $processeList = [
            new Process(['php', 'artisan', 'debug:test1', 'value1']),
            new Process(['php', 'artisan', 'debug:test1', 'value2']),
        ];

        foreach ($processeList as $i => $process) {
            $process->start(); // bashを実行
            sleep(1); // プロセスが順番に起動させる為にwait
        }

        do {
            $isRunning = false;
            foreach ($processeList as $i => $process) {
                echo $process->getIncrementalOutput(); // 標準出力
                if ($process->isRunning()) {
                    $isRunning = true;
                }
            }
        } while($isRunning);
    }
}
