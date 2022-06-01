<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    private const LOG_CAHNNEL_NAME = 'errorlog';

    /**
     * A list of Http Error Message.
     *
     * @var array
     */
    protected $httpErrorsMessage = [
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        413 => 'Payload Too Large',
        415 => 'Unsupported Media Type',
        422 => 'Unprocessable Entity',
        429 => 'Too Many Requests',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required'
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }


    // TODO ログ出力先などを変更する時は下記のメソッドをオーバーライドする。
    /**
     * Report or log an exception.
     *
     * @param  Throwable  $e
     * @return void
     *
     * @throws Throwable
     */
    /* public function report(Throwable $e)
    {
        if (config('app.env') === 'productinon') {
            parent::report($e);
        }
    } */

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Throwable|Symfony\Component\HttpKernel\Exception\HttpExceptionInterface  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws Throwable
     */
    public function render($request, Throwable|HttpExceptionInterface $e)
    {
        if (config('app.env') !== 'testing') {
            // エラーログの出力
            // Log::channel(self::LOG_CAHNNEL_NAME)->error('Error:', $request->toArray());
            Log::channel(self::LOG_CAHNNEL_NAME)->error('Error:', [$e->getMessage()]);
        }

        // HttpExceptionクラスの場合
        if ($this->isHttpException($e)) {
            $status = $e->getStatusCode();
            if (!$message = $e->getMessage()) {
                $message = $this->httpErrorsMessage[$status];
            }
            $response = [
                'status' => $status,
                'errors' => [],
                'message' => $message
            ];
            return response($response, $status);
        }

        return parent::render($request, $e);
    }
}
