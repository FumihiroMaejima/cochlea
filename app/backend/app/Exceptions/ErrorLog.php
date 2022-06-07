<?php

namespace App\Exceptions;

use App\Library\TimeLibrary;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;
use Exception;


class ErrorLog
{
    private const LOG_CAHNNEL_NAME = 'errorlog';

    // log出力項目
    private string $requestDateTime;
    private string $message;
    private int|false $pid;
    private string $memory;
    private string $stackTrace;

    /**
     * constructer.
     *
     * @param string $message message
     * @return void
     */
    public function __construct(
        string $message = '',
    ) {
        $this->requestDateTime = TimeLibrary::getCurrentDateTime();
        $this->message         = $message;
        $this->pid             = getmypid();
        $this->memory = (string)memory_get_peak_usage();
        $this->stackTrace      = getmypid();

        $this->outputLog();
    }

    /**
     * output error log in log file.
     * @return void
     */
    private function outputLog(): void
    {
        $context = [
            'request_datetime' => $this->requestDateTime,
            'message'          => $this->message,
            'process_id'       => $this->pid,
            'peak_memory'      => $this->memory,
            'stackTrace'       => $this->stackTrace,
        ];

        Log::channel(self::LOG_CAHNNEL_NAME)->error('Error:', $context);
    }
}
