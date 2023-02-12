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

    // log出力項目
    private string $requestDateTime;
    private string $method;
    private string $host;
    private string $ip;
    private string $uri;
    private string|null $contentType;
    private int $statusCode;
    private string $responseTime;
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
        $this->host            = $request->getHost();
        $this->ip              = $request->getClientIp();
        $this->method          = $request->getMethod();
        $this->uri             = $request->getRequestUri();
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
            'method'           => $this->method,
            'request_datetime' => $this->requestDateTime,
            'host'             => $this->host,
            'uri'              => $this->uri,
            'ip'               => $this->ip,
            'content_type'     => $this->contentType,
            'status_code'      => $this->statusCode,
            'response_time'    => $this->responseTime,
            'headers'          => $this->headers,
            'request_content'  => $this->requestContent,
            'plathome'         => $this->plathome,
            'process_id'       => $this->pid,
            'memory'           => $this->memory . ' Byte',
            'peak_memory'      => $this->peakMemory . ' Byte',
        ];

        // Log::debug($request->method(), ['url' => $request->fullUrl(), 'request' => $request->all()]);
        Log::channel(self::LOG_CAHNNEL_NAME)->info('Access:', $context);
    }
}
