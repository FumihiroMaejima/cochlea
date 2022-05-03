<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class AccessLog
{
    private const LOG_CAHNNEL_NAME = 'accesslog';

    // log出力項目
    private string $requestDateTime;
    private string $method;
    private string $host;
    private string $ip;
    private string $uri;
    private string|null $contentType;
    private string $responseTime;
    private mixed $requestContent;
    private string $plathome;
    private int|false $pid;
    private string $memory;

    private array $excludes = [
        '_debugbar',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
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
        $this->memory = (string)memory_get_peak_usage();


        // log出力
        $this->outputLog();

        return $response;
    }

    /**
     * check current path is log exclude path.
     * @param Request $request
     * @return bool
     */
    private function isExcludePath(Request $request): bool
    {
        return in_array($request->path(), $this->excludes, true);
    }

    /**
     * get log parameter from request.
     * @param Request $request
     */
    private function getLogParameterByRequest(Request $request): void
    {
        $this->host            = $request->getHost();
        $this->ip              = $request->getClientIp();
        $this->method          = $request->getMethod();
        $this->uri             = $request->getRequestUri();
        $this->contentType     = $request->getContentType();
        $this->plathome        = $request->userAgent() ?? '';
        $this->requestContent  = $request->getContent();
    }

    /**
     * get log parameter from response.
     * @param Request $response
     */
    // private function getLogParameterByResponse(Response $response): void {}



    /**
     * output access log in log file.
     * @param Request $request
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
            'response_time'    => $this->responseTime,
            'request_content'  => $this->requestContent,
            'plathome'         => $this->plathome,
            'process_id'       => $this->pid,
            'peak_memory'      => $this->memory,
        ];

        // Log::debug($request->method(), ['url' => $request->fullUrl(), 'request' => $request->all()]);
        Log::channel(self::LOG_CAHNNEL_NAME)->info('Access:', $context);
    }
}
