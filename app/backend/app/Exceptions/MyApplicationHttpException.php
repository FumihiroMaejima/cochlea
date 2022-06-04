<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

// class MyApplicationHttpException extends RuntimeException implements HttpExceptionInterface
class MyApplicationHttpException extends HttpException
{
    private int $statusCode;
    private array $headers;

    /**
     * Application Http Exception class.
     *
     * @param int $statusCode status code
     * @param string $message message
     * @param Throwable|null previous throwable
     * @param array $headers headers
     * @param int $code code
     * @return void
     */
    public function __construct(
        int $statusCode,
        string $message = '',
        Throwable $previous = null,
        array $headers = [],
        int $code = 0
    )
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;

        parent::__construct($statusCode, $message, $previous, $headers, $code);
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
}
