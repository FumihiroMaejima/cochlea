<?php

namespace App\Exceptions;

use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

// class MyApplicationHttpException extends RuntimeException implements HttpExceptionInterface
class MyApplicationHttpException extends HttpException
{
    private const LOG_CAHNNEL_NAME = 'errorlog';

    // ステータスコード
    private int $statusCode;

    // ヘッダー情報
    private array $headers;

    // メッセージ
    private string $exceptionMessage;

    /**
     * Application Http Exception class.
     *
     * @param int $statusCode status code
     * @param string $message message
     * @param bool $isResponseMessage if true, $message is output to response, false, output to log.
     * @param Throwable|null previous throwable
     * @param array $headers headers
     * @param int $code code
     * @return void
     */
    public function __construct(
        int $statusCode,
        string $message = '',
        bool $isResponseMessage = false,
        Throwable $previous = null,
        array $headers = [],
        int $code = 0
    ) {
        $this->statusCode = $statusCode;
        $this->headers = $headers;

        // レスポンスとして返す場合
        if ($isResponseMessage) {
            $this->setExceptionMessage($message);
        } else {
            // ログに出力
            $this->setErrorLog($message);
            $this->setExceptionMessage('');
        }

        parent::__construct($statusCode, $this->getExceptionMessage(), $previous, $headers, $code);
    }

    /**
     * get status code.
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * get headers.
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * set headers.
     *
     * @param array $headers header data.
     * @return void
     */
    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

    /**
     * get exception message.
     *
     * @return string
     */
    private function getExceptionMessage(): string
    {
        return $this->exceptionMessage;
    }

    /**
     * set exception message.
     *
     * @param string $message message.
     * @return void
     */
    private function setExceptionMessage(string $message): void
    {
        $this->exceptionMessage = $message;
    }

    /**
     * set message to error log.
     *
     * @param string $message log message.
     * @return void
     */
    private function setErrorLog(string $message): void
    {
        if (config('app.env') !== 'testing') {
            // エラーログの出力
            Log::channel(self::LOG_CAHNNEL_NAME)->error('Error:', [$message]);
        }
    }
}
