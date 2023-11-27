<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use App\Library\Log\LogLibrary;
use App\Library\Time\TimeLibrary;
use App\Library\Log\AccessLogLibrary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AccessLog
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (AccessLogLibrary::isExcludePath($request->path())) {
            return $next($request);
        }

        // $this->host = getmypid();
        $requestDateTime = TimeLibrary::getCurrentDateTime();
        $pid             = getmypid();

        [
            $uri,
            $method,
            $host,
            $ip,
            $contentType,
            $plathome,
            $headers,
            $requestContent,
        ] = AccessLogLibrary::getLogParameterByRequest($request);


        // 処理速度の計測
        $startTime = microtime(true);

        $response = $next($request);

        $responseTime = (string)(microtime(true) - $startTime);
        $memory = memory_get_usage();
        $peakMemory = memory_get_peak_usage();

        [$statusCode] = AccessLogLibrary::getLogParameterByResponse($response);

        // log出力
        AccessLogLibrary::outputLog(
            $requestDateTime,
            $uri,
            $method,
            $statusCode,
            $responseTime,
            $host,
            $ip,
            $contentType,
            $headers,
            $requestContent,
            $plathome,
            $pid,
            $memory,
            $peakMemory
        );

        return $response;
    }
}
