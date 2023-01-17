<?php

namespace App\Http\Middleware;

use Closure;
use App\Library\Time\TimeLibrary;
use App\Trait\CheckHeaderTrait;
use Illuminate\Http\Request;

class FakerTime
{
    use CheckHeaderTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // productionでは対応しない
        if (config('app.env') === 'productinon') {
            return $next($request);
        } else {
            $timeStamp = self::getFakerTimeStamp($request);
            if (!is_null($timeStamp)) {
                // 仮の時刻を設定する
                TimeLibrary::setFakerTimeStamp($timeStamp);
            }
            return $next($request);
        }

    }
}
