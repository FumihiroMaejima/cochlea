<?php

namespace App\Services\Admins;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use App\Exceptions\MyApplicationHttpException;
use App\Http\Requests\Admins\Debug\DebugFileUploadRequest;
use App\Library\File\ImageLibrary;
use App\Library\Stripe\StripeLibrary;
use App\Library\Time\TimeLibrary;
use App\Library\String\UnidLibrary;
use App\Models\Masters\Images;

class ImageService
{
    protected string $prop;

    /**
     * create PermissionsService instance
     * @return void
     */
    public function __construct()
    {
        $this->prop = 'debug propaty';
    }

    /**
     * 画像ファイルのアップロード
     *
     * @param DebugFileUploadRequest $request
     * @return JsonResponse
     */
    public function uploadImage(DebugFileUploadRequest $request): JsonResponse
    {
        try{
            // アップロードするディレクトリ名を指定
            // $uploadDirectory = '/images/debug/';
            $uploadDirectory = Config::get('myappFile.upload.storage.local.images.debug');

            /** @var UploadedFile $file */
            $file = $request->image;

            // ファイルが存在しない場合
            if (is_null($file)) {
                return response()->json(['message' => 'No file uploaded!', 'status' => 200], 200);
            }

            $resource = ImageLibrary::getFileResource($file);


            // TODO DBへの登録処理

            // ファイル名
            $fileName = $resource[Images::UUID] . '.' . $resource[Images::EXTENTION];
            // ファイルの格納(公開する場合はオプションとして’public’を指定する。)
            // $request->file('image')->storeAs($uploadDirectory, $fileName, 'public');
            // $request->file('image')->storeAs($uploadDirectory, $fileName);
            $result = $file->storeAs($uploadDirectory, $fileName);
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'message: ' . json_encode($e->getMessage()));
            throw $e;
        }

        $status = !$result ? 503 : 200;
        return response()->json(['message' => $result, 'status' => $status], $status);
    }
}
