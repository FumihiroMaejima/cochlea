<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
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
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // RateLimiting
        $this->configureRateLimiting();
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
