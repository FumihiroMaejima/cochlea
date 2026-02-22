<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * 1ユーザー又は1IPアドレス当たりのリクエスト回数制限
     *
     * Laravelのデフォルトは1分回に60回。
     *
     * @var int
     */
    private const MAX_ATTEMPT_REQUEST_COUNT = 100;

    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            // \Illuminate\Routing\Middleware\ThrottleRequestsのリクエスト数制限の設定
            // return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
            return Limit::perMinute(self::MAX_ATTEMPT_REQUEST_COUNT)->by($request->user()?->id ?: $request->ip());
        });
    }
}
