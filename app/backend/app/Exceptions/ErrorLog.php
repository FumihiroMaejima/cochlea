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

    // ログキー
    private const LOG_KEY_REQUEST_DATETIME = 'request_datetime';
    private const LOG_KEY_REQUEST_MESSAGE = 'message';
    private const LOG_KEY_REQUEST_URI = 'uri';
    private const LOG_KEY_REQUEST_PROCESS_ID = 'process_id';
    private const LOG_KEY_REQUEST_MEMORY = 'memory';
    private const LOG_KEY_REQUEST_PEAK_MEMORY = 'peak_memory';
    private const LOG_KEY_REQUEST_STACK_TRACE = 'stackTrace';
    private const LOG_KEY_REQUEST_REQUEST_PARAMETER = 'request_parameter';

    // log出力項目
    private string $requestDateTime;
    private ?string $uri = null;
    private string $message;
    private int|false $pid;
    private string $memory;
    private string $peakMemory;
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
        $this->memory = (string)memory_get_usage();
        $this->peakMemory = (string)memory_get_peak_usage();
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
            self::LOG_KEY_REQUEST_DATETIME          => $this->requestDateTime,
            self::LOG_KEY_REQUEST_URI               => $this->uri ?? null,
            self::LOG_KEY_REQUEST_MESSAGE           => $this->message,
            self::LOG_KEY_REQUEST_PROCESS_ID        => $this->pid,
            self::LOG_KEY_REQUEST_MEMORY            => $this->memory . ' Byte',
            self::LOG_KEY_REQUEST_PEAK_MEMORY       => $this->peakMemory . ' Byte',
            self::LOG_KEY_REQUEST_STACK_TRACE       => $this->stackTrace,
            self::LOG_KEY_REQUEST_REQUEST_PARAMETER => $this->parameter,
        ];

        Log::channel(self::LOG_CAHNNEL_NAME)->error('Error:', $context);
    }
}
