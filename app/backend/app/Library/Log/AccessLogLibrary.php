<?php

namespace App\Library\Log;

use Closure;
use App\Library\Log\LogLibrary;
use App\Library\Time\TimeLibrary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AccessLogLibrary
{
    private const LOG_CAHNNEL_NAME = 'accesslog';

    private const AUTHORIZATION_HEADER_KEY = 'authorization';
    private const AUTHORIZATION_HEADER_VALUE_SUFFIX = '*****';
    private const AUTHORIZATION_HEADER_VALUE_START_POSITION = 0;
    private const AUTHORIZATION_HEADER_VALUE_END_POSITION = 10;

    // ログキー
    private const LOG_KEY_REQUEST_DATETIME = 'request_datetime';
    private const LOG_KEY_REQUEST_URI = 'uri';
    private const LOG_KEY_REQUEST_METHOD = 'method';
    private const LOG_KEY_REQUEST_STATUS_CODE = 'status_code';
    private const LOG_KEY_REQUEST_RESPONSE_TIME = 'response_time';
    private const LOG_KEY_REQUEST_HOST = 'host';
    private const LOG_KEY_REQUEST_IP = 'ip';
    private const LOG_KEY_REQUEST_CONTENT_TYPE = 'content_type';
    private const LOG_KEY_REQUEST_HEADERS = 'headers';
    private const LOG_KEY_REQUEST_REQUEST_CONTENT = 'request_content';
    private const LOG_KEY_REQUEST_PLATHOME = 'plathome';
    private const LOG_KEY_REQUEST_PROCESS_ID = 'process_id';
    private const LOG_KEY_REQUEST_MEMORY_BYTE = 'memory_byte';
    private const LOG_KEY_REQUEST_PEAK_MEMORY_BYTE = 'peak_memory_byte';

    // log出力項目
    private string $requestDateTime;
    private string $uri;
    private string $method;
    private int $statusCode;
    private string $responseTime;
    private string $host;
    private string $ip;
    private string|null $contentType;
    private string|array|null $headers;
    private mixed $requestContent;
    private string $plathome;
    private int|false $pid;
    private int $memory;
    private int $peakMemory;

    private const ECLUDE_PATH_LIST = [
        '_debugbar',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (self::isExcludePath($request)) {
            return $next($request);
        }

        // $this->host = getmypid();
        $this->requestDateTime = TimeLibrary::getCurrentDateTime();
        $this->pid             = getmypid();

        $this->getLogParameterByRequest($request);


        // 処理速度の計測
        $startTime = microtime(true);

        $response = $next($request);

        $this->responseTime = microtime(true) - $startTime;
        $this->memory = memory_get_usage();
        $this->peakMemory = memory_get_peak_usage();

        $this->getLogParameterByResponse($response);


        // log出力
        self::outputLog(
            $this->requestDateTime,
            $this->uri,
            $this->method,
            $this->statusCode,
            $this->responseTime,
            $this->host,
            $this->ip,
            $this->contentType,
            $this->headers,
            $this->requestContent,
            $this->plathome,
            $this->pid,
            $this->memory,
            $this->peakMemory
        );

        return $response;
    }

    /**
     * check current path is log exclude path.
     *
     * @param Request $request
     * @return bool
     */
    private static function isExcludePath(Request $request): bool
    {
        return in_array($request->path(), self::ECLUDE_PATH_LIST, true);
    }

    /**
     * get log parameter from request.
     *
     * @param Request $request
     * @return void
     */
    private function getLogParameterByRequest(Request $request): void
    {
        $contentType = $request->getContentType();
        $this->uri             = $request->getRequestUri();
        $this->method          = $request->getMethod();
        $this->host            = $request->getHost();
        $this->ip              = $request->getClientIp();
        $this->contentType     = $contentType;
        $this->plathome        = $request->userAgent() ?? '';
        $this->headers         = self::getRequestHeader($request->header());
        $this->requestContent  = LogLibrary::maskingSecretKeys($request->all());
    }

    /**
     * get log parameter from response.
     *
     * @param RedirectResponse|Response|JsonResponse|BinaryFileResponse $response
     * @return void
     */
    private function getLogParameterByResponse(
        RedirectResponse | Response | JsonResponse | BinaryFileResponse $response
    ): void {
        $this->statusCode = $response->getStatusCode();
    }

    /**
     * get request header.
     *
     * @param string|array|null $headers header contents. (\Illuminate\Http\Request->header())
     * @return string|array|null
     */
    public static function getRequestHeader(string|array|null $headers): string|array|null
    {
        if (is_array($headers)) {
            $response = [];
            foreach ($headers as $key => $value) {
                if ($key === self::AUTHORIZATION_HEADER_KEY) {
                    // $valueは配列になる想定
                    $response[$key] = mb_substr(
                        $value[0],
                        self::AUTHORIZATION_HEADER_VALUE_START_POSITION,
                        self::AUTHORIZATION_HEADER_VALUE_END_POSITION
                    ) . self::AUTHORIZATION_HEADER_VALUE_SUFFIX;
                } else {
                    $response[$key] = $value;
                }
            }

            return $response;
        } else {
            return $headers;
        }
    }

    /**
     * output access log in log file.
     *
     * @param string $requestDateTime
     * @param string $uri
     * @param string $method
     * @param string $statusCode
     * @param string $responseTime
     * @param string $host
     * @param string $ip
     * @param ?string $contentType
     * @param string|array|null $headers
     * @param mixed $requestContent
     * @param string $plathome
     * @param int|bool $pid
     * @param int $memory
     * @param int $peakMemory
     * @return void
     */
    public static function outputLog(
        string $requestDateTime,
        string $uri,
        string $method,
        int $statusCode,
        string $responseTime,
        string $host,
        string $ip,
        ?string $contentType,
        string|array|null $headers,
        mixed $requestContent,
        string $plathome,
        int|bool $pid,
        int $memory,
        int $peakMemory
    ): void {
        $context = [
            self::LOG_KEY_REQUEST_DATETIME         => $requestDateTime,
            self::LOG_KEY_REQUEST_URI              => $uri,
            self::LOG_KEY_REQUEST_METHOD           => $method,
            self::LOG_KEY_REQUEST_STATUS_CODE      => $statusCode,
            self::LOG_KEY_REQUEST_RESPONSE_TIME    => $responseTime,
            self::LOG_KEY_REQUEST_HOST             => $host,
            self::LOG_KEY_REQUEST_IP               => $ip,
            self::LOG_KEY_REQUEST_CONTENT_TYPE     => $contentType,
            self::LOG_KEY_REQUEST_HEADERS          => $headers,
            self::LOG_KEY_REQUEST_REQUEST_CONTENT  => $requestContent,
            self::LOG_KEY_REQUEST_PLATHOME         => $plathome,
            self::LOG_KEY_REQUEST_PROCESS_ID       => $pid,
            self::LOG_KEY_REQUEST_MEMORY_BYTE      => $memory,
            self::LOG_KEY_REQUEST_PEAK_MEMORY_BYTE => $peakMemory,
        ];

        Log::channel(self::LOG_CAHNNEL_NAME)->info('Access:', $context);
    }
}
