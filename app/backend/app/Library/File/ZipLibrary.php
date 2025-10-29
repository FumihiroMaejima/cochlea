<?php

declare(strict_types=1);

namespace App\Library\File;

use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use Exception;
use ZipArchive;

class ZipLibrary
{
    public const DIRECTORY = 'images/default/';

    /**
     * get csv file contents by SplFileObject
     *
     * @param array $fileList file name list.
     * @param string $fileName zip file name
     * @return string
     * @throws Exception
     */
    public static function getZipFileByParameterFileList(
        array $fileList,
        string $fileName = 'test1.zip'
    ): string {
        $zip = new ZipArchive();
        $zipFilePath = storage_path(self::DIRECTORY . $fileName);

        if ($zip->open($zipFilePath, ZipArchive::CREATE) !== true) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_500,
                "cannot open <$zipFilePath>\n"
            );
        }

        foreach ($fileList as $file) {
            $path = storage_path(self::DIRECTORY . $file);
            if (file_exists($path)) {
                $zip->addFile($path, basename($path));
                // ファイルストリームを読み込んで追加する場合(s3のgetObject等($object['body']で取得))
                // $zip->addFromString($path, file_get_contents($path));
            } else {
                throw new MyApplicationHttpException(
                    StatusCodeMessages::STATUS_500,
                    "File $path does not exist.\n"
                );
            }
        }

        $zip->close();
        return $zipFilePath;
    }
}
