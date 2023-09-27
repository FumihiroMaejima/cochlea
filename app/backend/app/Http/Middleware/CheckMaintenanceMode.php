<?php

namespace App\Http\Middleware;

use Closure;
use App\Library\Maintenance\MaintenanceLibrary;
use App\Library\Message\StatusCodeMessages;
use App\Exceptions\MyApplicationHttpException;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Closure  $next
     * @return mixed
     * @throws MyApplicationHttpException
     */
    public function handle($request, Closure $next)
    {
        // メンテナンス中か
        if (MaintenanceLibrary::isMaintenance() && MaintenanceLibrary::isMaintenancePeriod()) {
            // 通過可能設定有りか
            if (MaintenanceLibrary::isEnabplePass()) {
                $path = $request->getRequestUri();
                $ip = $request->getClientIp();
                if (MaintenanceLibrary::isExceptRoute($path) ||
                    MaintenanceLibrary::isExceptIp($ip)
                ) {
                    return $next($request);
                }
            }

            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_503,
                'Now Maintenance.'
            );
        }

        return $next($request);
    }
}
