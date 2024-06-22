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
}
