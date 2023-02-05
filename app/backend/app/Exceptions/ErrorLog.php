<?php

namespace App\Exceptions;

use App\Library\Time\TimeLibrary;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
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
    private array $parameter;

    /**
     * constructer.
     *
     * @param Throwable|HttpExceptionInterface $error error
     * @param array $parameter error data exmple: request parameter
     * @return void
     */
    public function __construct(
        Throwable|HttpExceptionInterface $error,
        array $parameter = []
    ) {
        $this->requestDateTime = TimeLibrary::getCurrentDateTime();
        $this->message         = $error->getMessage();
        $this->pid             = getmypid();
        $this->memory = (string)memory_get_peak_usage();
        $this->stackTrace      = str_replace("\n", '', $error->getTraceAsString()); // １行で表示させる
        $this->parameter       = $parameter;

        $this->outputLog();
    }

    /**
     * output error log in log file.
     * @return void
     */
    private function outputLog(): void
    {
        $context = [
            'request_datetime'  => $this->requestDateTime,
            'message'           => $this->message,
            'process_id'        => $this->pid,
            'peak_memory'       => $this->memory,
            'stackTrace'        => $this->stackTrace,
            'request_parameter' => $this->parameter,
        ];

        Log::channel(self::LOG_CAHNNEL_NAME)->error('Error:', $context);
    }
}
