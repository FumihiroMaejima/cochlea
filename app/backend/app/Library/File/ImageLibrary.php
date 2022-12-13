<?php

namespace App\Library\File;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use App\Exceptions\MyApplicationHttpException;
use App\Http\Requests\Admin\Debug\DebugFileUploadRequest;
use App\Models\Masters\Images;
use App\Library\Random\RandomStringLibrary;
use App\Library\Stripe\StripeLibrary;
use App\Library\String\UuidLibrary;
use App\Library\Time\TimeLibrary;

class ImageLibrary
{
    /**
     * 画像ファイルのアップロード
     *
     * @param UploadedFile $file
     * @return array<string, string>
     * @throws Exception
     */
    public static function getFileResource(UploadedFile $file): array
    {
        $uuid = UuidLibrary::uuidVersion4();

        // オリジナルファイル名
        $originalName = $file->getClientOriginalName();

        // 拡張子
        $extention = $file->getClientOriginalExtension();

        // mimeType
        $mimeType = $file->getMimeType();

        // S3キー
        $s3key = RandomStringLibrary::getByHashRandomString(RandomStringLibrary::RANDOM_STRING_LENGTH_24);


        return [
            Images::UUID      => $uuid,
            Images::NAME      => $originalName,
            Images::EXTENTION => $extention,
            Images::MIME_TYPE => $mimeType,
            Images::S3_KEY    => $s3key,
        ];
    }
}
