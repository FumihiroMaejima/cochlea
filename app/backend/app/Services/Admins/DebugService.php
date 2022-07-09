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
use App\Library\Stripe\StripeLibrary;

class DebugService
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
     * get permissions data for frontend parts
     *
     * @param  \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function getList(): JsonResponse
    {
        return StripeLibrary::getTestList();
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
            $uploadDirectory = 'debug/';

            /** @var UploadedFile $file */
            $file = $request->image;


            // ファイル名
            $fileName = $file->getClientOriginalName();
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
