<?php

namespace App\Http\Middleware;

use Closure;
use App\Library\Log\LogLibrary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AccessLog
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
    private const LOG_KEY_REQUEST_MEMORY = 'memory';
    private const LOG_KEY_REQUEST_PEAK_MEMORY = 'peak_memory';

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
    private string $memory;
    private string $peakMemory;

    private array $excludes = [
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
        if ($this->isExcludePath($request)) {
            return $next($request);
        }

        // $this->host = getmypid();
        $this->requestDateTime = now()->format('Y-m-d H:i:s');
        $this->pid             = getmypid();

        $this->getLogParameterByRequest($request);


        // 処理速度の計測
        $startTime = microtime(true);

        $response = $next($request);

        $this->responseTime = microtime(true) - $startTime;
        $this->memory = (string)memory_get_usage();
        $this->peakMemory = (string)memory_get_peak_usage();

        $this->getLogParameterByResponse($response);


        // log出力
        $this->outputLog();

        return $response;
    }

    /**
     * check current path is log exclude path.
     *
     * @param Request $request
     * @return bool
     */
    private function isExcludePath(Request $request): bool
    {
        return in_array($request->path(), $this->excludes, true);
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
     * @param string|array|null $headers header contents.
     * @return string|array|null
     */
    private static function getRequestHeader(string|array|null $headers): string|array|null
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
     * @return void
     */
    private function outputLog(): void
    {
        $context = [
            self::LOG_KEY_REQUEST_DATETIME         => $this->requestDateTime,
            self::LOG_KEY_REQUEST_URI              => $this->uri,
            self::LOG_KEY_REQUEST_METHOD           => $this->method,
            self::LOG_KEY_REQUEST_STATUS_CODE      => $this->statusCode,
            self::LOG_KEY_REQUEST_RESPONSE_TIME    => $this->responseTime,
            self::LOG_KEY_REQUEST_HOST             => $this->host,
            self::LOG_KEY_REQUEST_IP               => $this->ip,
            self::LOG_KEY_REQUEST_CONTENT_TYPE     => $this->contentType,
            self::LOG_KEY_REQUEST_HEADERS          => $this->headers,
            self::LOG_KEY_REQUEST_REQUEST_CONTENT  => $this->requestContent,
            self::LOG_KEY_REQUEST_PLATHOME         => $this->plathome,
            self::LOG_KEY_REQUEST_PROCESS_ID       => $this->pid,
            self::LOG_KEY_REQUEST_MEMORY           => $this->memory . ' Byte',
            self::LOG_KEY_REQUEST_PEAK_MEMORY      => $this->peakMemory . ' Byte',
        ];

        // Log::debug($request->method(), ['url' => $request->fullUrl(), 'request' => $request->all()]);
        Log::channel(self::LOG_CAHNNEL_NAME)->info('Access:', $context);
    }
}
