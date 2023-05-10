<?php

namespace App\Http\Middleware;

use Closure;
use App\Library\Log\LogLibrary;
use App\Library\Log\AccessLogLibrary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AccessLog
{
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
        AccessLogLibrary::outputLog(
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
        $this->headers         = AccessLogLibrary::getRequestHeader($request->header());
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
}
