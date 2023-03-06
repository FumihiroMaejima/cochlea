<?php

namespace App\Library\Log;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;

class BannerLibrary
{
    public const EXTENTION = 'png';
    public const DIRECTORY = 'images/';
    public const DEFAULT_FILE_IMAGE_NAME_1 = '200x600px_default1';
    public const DEFAULT_FILE_IMAGE_NAME_2 = '200x600px_default2';
    public const DEFAULT_FILE_IMAGE_NAME_3 = '200x600px_default3';

    public const FILE_NAME_ACCESS = 'access';
    public const FILE_NAME_ERROR = 'error';

    // リクエストパラメーターがログ出力可能なContent-type(ファイルアップロードなど'form'になっている場合はリクエストパラメーターはログに出力しない)
    public const LOG_OUTPUTABLE_CONTENT_TYPE = [null, 'json'];
    public const SECRET_KEYS = [
        'email' => 'email',
        'password' => 'password',
        'password_confirmation' => 'password_confirmation',
        'email' => 'email',
        'token' => 'token',
        'tokenPayload' => 'tokenPayload',
        'tokenHeader' => 'tokenHeader',
    ];

    /**
     * get banner default image.
     *
     * @return string
     */
    public static function getDefaultBanner(): string
    {
        $path = self::DIRECTORY . self::DEFAULT_FILE_IMAGE_NAME_1 . self::EXTENTION;

        // storage/app直下に無い為file_get_contents()で取得
        $file = file_get_contents(storage_path($path));

        if (is_null($file)) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_404,
                'File Not Exist.'
            );
        }

        return $file;
    }
}
