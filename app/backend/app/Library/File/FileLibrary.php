<?php

namespace App\Library\File;

use Illuminate\Support\Facades\Storage;

class FileLibrary
{
    // storage disk
    public const STORAGE_DISK_LOCAL = 'local';
    public const STORAGE_DISK_S3 = 's3';

    /**
     * get storage disk by env
     *
     * @return string
     */
    public static function getStorageDiskByEnv(): string
    {
        if ((config('app.env') === 'local') || config('app.env') === 'testing') {
            return self::STORAGE_DISK_LOCAL;
        } else {
            return self::STORAGE_DISK_S3;
        }
    }

    /**
     * get storage disk by env
     *
     * @param string $path file path
     * @return string|null
     */
    public static function getFileStoream(string $path): string|null
    {
        // storageの存在確認
        $file = Storage::disk('local')->get($path);

        // production向けなどS3から取得する時の設定
        if (!((config('app.env') === 'local') || config('app.env') === 'testing')) {
            if (is_null($file)) {
                // productionの時はenvでデフォルトのストレージを変更するのが適切
                $file = Storage::disk(self::STORAGE_DISK_S3)->get($path);
                // ファイルデータそのものを別途レスポンスに返す時はローカルに保存する
                Storage::disk(self::STORAGE_DISK_LOCAL)->put($path, $file, self::STORAGE_DISK_LOCAL);
            }
        }

        return $file;
    }
}
