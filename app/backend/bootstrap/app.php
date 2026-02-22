<?php

use App\Exceptions\MyApplicationHttpException;
use App\Library\Log\BatchLogLibrary;
use App\Library\Log\ErrorLogLibrary;
use App\Library\Message\StatusCodeMessages;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
// use Throwable;
// use ErrorException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
        // 旧`app/Http/Kernel.php`の$middleware内容を移行
        $middleware->use([
            \Illuminate\Foundation\Http\Middleware\InvokeDeferredCallbacks::class,
            // \Illuminate\Http\Middleware\TrustHosts::class,
            \Illuminate\Http\Middleware\TrustProxies::class,
            \Illuminate\Http\Middleware\HandleCors::class,
            \Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance::class,
            \Illuminate\Http\Middleware\ValidatePostSize::class,
            \Illuminate\Foundation\Http\Middleware\TrimStrings::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        ]);

        // middlewareGroupsの設定
        $middleware->group('web', [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);
        $middleware->group('api', [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            // Laravelセッションを利用する場合はStartSession,ShareErrorsFromSessionのコメントアウトを外す
            // \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        // OAuthをAPIで利用する為にはLaravelのキャッシュが必要
        $middleware->group('oauth_api', [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
            'customAuth' => \App\Http\Middleware\CustomAuthenticate::class,
            'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
            'can' => \Illuminate\Auth\Middleware\Authorize::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
            'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            // custom (旧config/app.phpに記述分)
            'Excel' => Maatwebsite\Excel\Facades\Excel::class,
            'Socialite' => Laravel\Socialite\Facades\Socialite::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // ログ出力
        $exceptions->report(function (Throwable $e) {
            // ...
            // $this->shouldntReport($e);
            if ($e instanceof HttpExceptionInterface) {
                $message = $e->getMessage();
                $status = $e->getStatusCode();
                if (config('app.env') !== 'testing' && $message !== '') {
                    // エラーログの出力
                    ErrorLogLibrary::exec($e, $status);
                } else {
                    // その他の例外は親クラスの処理を実行
                    parent::report($e);
                }
            } elseif ($e instanceof ErrorException) {
                // TODO バッチ処理は個別のExceptionクラスを投げる様にする。
                BatchLogLibrary::exec($e, $e->getCode());
            } else {
                // TODO ログの細分化
                // 汎用エラーログの出力
                ErrorLogLibrary::exec($e, $e->getCode());
            }
        });

        // レンダリング
        $exceptions->render(function (Throwable $e, Request $request) {
            $isHttpException = $e instanceof HttpExceptionInterface;
            $message = $e->getMessage();
            $status = $isHttpException ? $e->getStatusCode() : $e->getCode();
            if ((MyApplicationHttpException::isThisException($e)) && (config('app.env') !== 'testing' && $message !== '')) {
                // $status = $e->getStatusCode();
                // エラーログの出力
                ErrorLogLibrary::exec($e, $status);
            }

            // HttpExceptionクラスの場合
            if ($isHttpException) {
                if ($message === '') {
                    $message = StatusCodeMessages::HTTP_STATUS_MESSAGE_LIST[$status] ?? 'Unknown Error';
                }
                $response = [
                    'status' => $status,
                    'errors' => [],
                    'message' => $message
                ];
                return response($response, $status);
            }
        });
    })->create();
