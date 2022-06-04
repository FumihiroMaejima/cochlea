<?php

namespace App\Exceptions;

class ExceptionStatusCodeMessages
{
    // ステータスコード
    public const STATUS_CODE_400 = 400;
    public const STATUS_CODE_401 = 401;
    public const STATUS_CODE_402 = 402;
    public const STATUS_CODE_403 = 403;
    public const STATUS_CODE_404 = 404;
    public const STATUS_CODE_405 = 405;
    public const STATUS_CODE_406 = 406;
    public const STATUS_CODE_407 = 407;
    public const STATUS_CODE_408 = 408;
    public const STATUS_CODE_413 = 413;
    public const STATUS_CODE_415 = 415;
    public const STATUS_CODE_422 = 422;
    public const STATUS_CODE_429 = 429;
    public const STATUS_CODE_500 = 500;
    public const STATUS_CODE_501 = 501;
    public const STATUS_CODE_502 = 502;
    public const STATUS_CODE_503 = 503;
    public const STATUS_CODE_504 = 504;
    public const STATUS_CODE_505 = 505;
    public const STATUS_CODE_506 = 506;
    public const STATUS_CODE_507 = 507;
    public const STATUS_CODE_508 = 508;
    public const STATUS_CODE_510 = 510;
    public const STATUS_CODE_511 = 511;

    // エラーメッセージ
    public const MESSAGE_400 = 'Bad Request';
    public const MESSAGE_401 = 'Unauthorized';
    public const MESSAGE_402 = 'Payment Required';
    public const MESSAGE_403 = 'Forbidden';
    public const MESSAGE_404 = 'Not Found';
    public const MESSAGE_405 = 'Method Not Allowed';
    public const MESSAGE_406 = 'Not Acceptable';
    public const MESSAGE_407 = 'Proxy Authentication Required';
    public const MESSAGE_408 = 'Request Timeout';
    public const MESSAGE_413 = 'Payload Too Large';
    public const MESSAGE_415 = 'Unsupported Media Type';
    public const MESSAGE_422 = 'Unprocessable Entity';
    public const MESSAGE_429 = 'Too Many Requests';
    public const MESSAGE_500 = 'Internal Server Error';
    public const MESSAGE_501 = 'Not Implemented';
    public const MESSAGE_502 = 'Bad Gateway';
    public const MESSAGE_503 = 'Service Unavailable';
    public const MESSAGE_504 = 'Gateway Timeout';
    public const MESSAGE_505 = 'HTTP Version Not Supported';
    public const MESSAGE_506 = 'Variant Also Negotiates';
    public const MESSAGE_507 = 'Insufficient Storage';
    public const MESSAGE_508 = 'Loop Detected';
    public const MESSAGE_510 = 'Not Extended';
    public const MESSAGE_511 = 'Network Authentication Required';


    /** @var array A list of Http Error Message. */
    public array $httpErrorsMessages = [
        self::STATUS_CODE_400 => self::MESSAGE_400,
        self::STATUS_CODE_401 => self::MESSAGE_401,
        self::STATUS_CODE_402 => self::MESSAGE_402,
        self::STATUS_CODE_403 => self::MESSAGE_403,
        self::STATUS_CODE_404 => self::MESSAGE_404,
        self::STATUS_CODE_405 => self::MESSAGE_405,
        self::STATUS_CODE_406 => self::MESSAGE_406,
        self::STATUS_CODE_407 => self::MESSAGE_407,
        self::STATUS_CODE_408 => self::MESSAGE_408,
        self::STATUS_CODE_413 => self::MESSAGE_413,
        self::STATUS_CODE_415 => self::MESSAGE_415,
        self::STATUS_CODE_422 => self::MESSAGE_422,
        self::STATUS_CODE_429 => self::MESSAGE_429,
        self::STATUS_CODE_500 => self::MESSAGE_500,
        self::STATUS_CODE_501 => self::MESSAGE_501,
        self::STATUS_CODE_502 => self::MESSAGE_502,
        self::STATUS_CODE_503 => self::MESSAGE_503,
        self::STATUS_CODE_504 => self::MESSAGE_504,
        self::STATUS_CODE_505 => self::MESSAGE_505,
        self::STATUS_CODE_506 => self::MESSAGE_506,
        self::STATUS_CODE_507 => self::MESSAGE_507,
        self::STATUS_CODE_508 => self::MESSAGE_508,
        self::STATUS_CODE_510 => self::MESSAGE_510,
        self::STATUS_CODE_511 => self::MESSAGE_511,
    ];
}
