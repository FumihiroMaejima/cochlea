<?php

namespace App\Library\Log;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Library\File\FileLibrary;
use App\Library\Random\RandomStringLibrary;
use App\Library\Stripe\StripeLibrary;
use App\Library\String\UuidLibrary;
use App\Library\Time\TimeLibrary;

class LogLibrary
{
    public const EXTENTION = 'log';
    public const DIRECTORY = 'logs/';

    public const FILE_NAME_ACCESS = 'access';
    public const FILE_NAME_ERROR = 'error';

    // リクエストパラメーターがログ出力可能なContent-type(ファイルアップロードなど'form'になっている場合はリクエストパラメーターはログに出力しない)
    public const LOG_OUTPUTABLE_CONTENT_TYPE = [null, 'json'];

    /**
     * get logfile contents.
     *
     * @param string|null $date
     * @param string $name
     * @return array
     */
    public static function getLogFileContentsList(?string $date = null, string $name = self::FILE_NAME_ACCESS): array
    {
        if (is_null($date)) {
            $date = TimeLibrary::getCurrentDateTime(TimeLibrary::DEFAULT_DATE_TIME_FORMAT_DATE_ONLY);
        }

        $path = self::DIRECTORY . "$name-$date." . self::EXTENTION;

        // storage/app直下に無い為file_get_contents()で取得
        $file = file_get_contents(storage_path($path));

        if (is_null($file)) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_404,
                'File Not Exist.'
            );
        }

        $fileContents = explode("\n", $file);

        return $fileContents;
    }

    /**
     * get logfile contents as Associative array(連想配列).
     *
     * @param string|null $date
     * @param string $name
     * @return array
     */
    public static function getLogFileContentAsAssociative(?string $date = null, string $name = self::FILE_NAME_ACCESS): array
    {
        $response = [];
        $logFileContetsList = self::getLogFileContentsList($date ?? null, $name ?? null);

        foreach ($logFileContetsList as $logRow) {
            $tmp = explode(' ', $logRow);
            if (!empty($tmp) && (count($tmp) >= 6)) {
                // 日時をkeyとして設定
                $head = $tmp[0] . ' ' . $tmp[1];
                $mainRowLog = '';
                for ($i = 4; $i < count($tmp); $i++) {
                    $mainRowLog .= $tmp[$i];
                }

                $rowDictionary = json_decode($mainRowLog, true);
                $response[$head] = $rowDictionary;
            }
        }

        return $response;
    }

    /**
     * whichever content type is able to output to log.
     *
     * @param ?string $contentType
     * @return bool
     */
    public static function isLoggableContentType(?string $contentType): bool
    {
        return in_array($contentType, self::LOG_OUTPUTABLE_CONTENT_TYPE, true);
    }
}
