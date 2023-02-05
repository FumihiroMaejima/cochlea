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
                $mainRowLog = $tmp[4] . $tmp[5];
                $rowItemList = explode(',', $mainRowLog);

                foreach($rowItemList as $item) {
                    $keyValues = explode(':', $item);
                    if (!empty($keyValues) && (count($keyValues) >= 2)) {
                        $value = '';
                        for ($i = 1; $i < count($keyValues); $i++) {
                            $value .= $keyValues[$i];
                        }
                        $response[$head][$keyValues[0]] = $value;
                    }
                }
            }
        }

        return $response;
    }
}
