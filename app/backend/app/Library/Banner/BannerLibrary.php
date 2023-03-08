<?php

namespace App\Library\Banner;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Models\Masters\Banners;

class BannerLibrary
{
    public const EXTENTION = 'png';
    public const DIRECTORY = 'images/';
    public const DIRECTORY_DEFAULT = 'default/';
    public const ADMIN_BANNER_PATH = '/api/v1/admin/banners/';
    public const USER_BANNER_PATH = '/api/v1/banners/';
    public const DEFAULT_FILE_IMAGE_NAME_200X600_1 = '200x600px_default1';
    public const DEFAULT_FILE_IMAGE_NAME_200X600_2 = '200x600px_default2';
    public const DEFAULT_FILE_IMAGE_NAME_200X600_3 = '200x600px_default3';
    public const DEFAULT_FILE_IMAGE_NAME_240X1200_1 = '240x1200px_default1';
    public const DEFAULT_FILE_IMAGE_NAME_240X1200_2 = '240x1200px_default2';
    public const DEFAULT_FILE_IMAGE_NAME_240X1200_3 = '240x1200px_default3';

    /**
     * get banner default image.
     *
     * @return string
     */
    public static function getDefaultBanner(): string
    {
        $path = self::DIRECTORY . self::DIRECTORY_DEFAULT . self::DEFAULT_FILE_IMAGE_NAME_200X600_1 . '.' . self::EXTENTION;

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

    /**
     * get banner default image path.
     *
     * @param bool $isRand is random default image
     * @return string
     */
    public static function getDefaultBannerPath(bool $isRand = false): string
    {
        if ($isRand) {
            $value = rand(1, 3);
            $path = self::DIRECTORY . self::DIRECTORY_DEFAULT . "200x600px_default$value" . '.' . self::EXTENTION;
        } else {
            $path = self::DIRECTORY . self::DIRECTORY_DEFAULT . self::DEFAULT_FILE_IMAGE_NAME_200X600_1 . '.' . self::EXTENTION;
        }
        return storage_path($path);
    }

    /**
     * get banner path at admin service.
     *
     * @param array $banner banner record
     * @return string
     */
    public static function getAdminBannerPath(array $banner): string
    {
        return config('app.url') . self::ADMIN_BANNER_PATH . $banner[Banners::UUID];
        ;
    }

    /**
     * get banner path at user service.
     *
     * @param array $banner banner record
     * @return string
     */
    public static function getUserServiceBannerPath(array $banner): string
    {
        return config('app.url') . self::USER_BANNER_PATH . $banner[Banners::UUID];
        ;
    }
}
