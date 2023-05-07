<?php

namespace App\Library\Log;

use App\Library\Time\TimeLibrary;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class ErrorLogLibrary
{
    private const LOG_CAHNNEL_NAME = 'errorlog';

    // $_SERVERのキー
    private const GLOBAL_VALUE_KEY_REQUEST_URI = 'REQUEST_URI';

    // ログキー
    private const LOG_KEY_REQUEST_DATETIME = 'request_datetime';
    private const LOG_KEY_REQUEST_MESSAGE = 'message';
    private const LOG_KEY_REQUEST_URI = 'uri';
    private const LOG_KEY_REQUEST_PROCESS_ID = 'process_id';
    private const LOG_KEY_REQUEST_MEMORY = 'memory';
    private const LOG_KEY_REQUEST_PEAK_MEMORY = 'peak_memory';
    private const LOG_KEY_REQUEST_STACK_TRACE = 'stackTrace';
    private const LOG_KEY_REQUEST_REQUEST_PARAMETER = 'request_parameter';

    /**
     * constructer.
     *
     * @param Throwable|HttpExceptionInterface $error error
     * @param array $parameter error data exmple: request parameter
     * @return void
     */
    public static function exec(
        Throwable|HttpExceptionInterface $error,
        array $parameter = []
    ) {
        $uri = isset($_SERVER[self::GLOBAL_VALUE_KEY_REQUEST_URI])
        ? $_SERVER[self::GLOBAL_VALUE_KEY_REQUEST_URI]
        : null;

        self::outputLog(
            TimeLibrary::getCurrentDateTime(),
            $uri,
            $error->getMessage(),
            getmypid(),
            (string)memory_get_usage(),
            (string)memory_get_peak_usage(),
            str_replace("\n", '', $error->getTraceAsString()), // １行で表示させる
            $parameter
        );
    }

    /**
     * output error log in log file.
     *
     * @param string $requestDateTime
     * @param ?string $uri
     * @param string $message
     * @param int|bool $pid
     * @param string $memory
     * @param string $peakMemory
     * @param string $stackTrace
     * @param string $parameter
     * @return void
     */
    private static function outputLog(
        string $requestDateTime,
        ?string $uri,
        string $message,
        int|bool $pid,
        string $memory,
        string $peakMemory,
        string $stackTrace,
        array $parameter
    ): void{
        $context = [
            self::LOG_KEY_REQUEST_DATETIME          => $requestDateTime,
            self::LOG_KEY_REQUEST_URI               => $uri ?? null,
            self::LOG_KEY_REQUEST_MESSAGE           => $message,
            self::LOG_KEY_REQUEST_PROCESS_ID        => $pid,
            self::LOG_KEY_REQUEST_MEMORY            => $memory . ' Byte',
            self::LOG_KEY_REQUEST_PEAK_MEMORY       => $peakMemory . ' Byte',
            self::LOG_KEY_REQUEST_STACK_TRACE       => $stackTrace,
            self::LOG_KEY_REQUEST_REQUEST_PARAMETER => $parameter,
        ];

        Log::channel(self::LOG_CAHNNEL_NAME)->error('Error:', $context);
    }
}
