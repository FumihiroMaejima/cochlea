<?php

namespace App\Services\Admins;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use App\Exceptions\MyApplicationHttpException;
use App\Exceptions\ExceptionStatusCodeMessages;
use App\Http\Requests\Admins\Debug\DebugFileUploadRequest;
use App\Library\File\ImageLibrary;
use App\Library\Time\TimeLibrary;
use App\Library\String\UuidLibrary;
use App\Models\Masters\Images;
use App\Repositories\Admins\Images\ImagesRepositoryInterface;
use App\Http\Resources\Admins\ImagesResource;
use \Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImagesService
{
    protected ImagesRepositoryInterface $imagesRepository;

    /**
     * create ImagesService instance
     *
     * ImagesRepositoryInterface $coinsRepository
     * @return void
     */
    public function __construct(ImagesRepositoryInterface $imagesRepository)
    {
        $this->imagesRepository = $imagesRepository;
    }

    /**
     * 画像ファイルのダウンロード
     *
     * @param string $uuid
     * @param int $version
     * @return BinaryFileResponse
     * @throws MyApplicationHttpException
     */
    public function getImage(string $uuid, int $version): BinaryFileResponse
    {
        $collection = $this->imagesRepository->getByUuid($uuid, $version);

        if (is_null($collection)) {
            throw new MyApplicationHttpException(
                ExceptionStatusCodeMessages::STATUS_CODE_404,
                'not found images.'
            );
        }

        $resource = ImagesResource::toArrayForGetFirstByUuid($collection);

        $name = $resource[IMAGES::UUID];
        $extention = $resource[IMAGES::EXTENTION];

        $directory = Config::get('myappFile.upload.storage.local.images.debug');

        $imagePath = "{$directory}{$name}.{$extention}";

        // storageの存在確認
        $file = Storage::get($imagePath);

        if (is_null($file)) {
            throw new MyApplicationHttpException(
                ExceptionStatusCodeMessages::STATUS_CODE_404,
            );
        }

        // return response()->file(Storage::path("{$directory}{$name}.{$extention}?ver={$version}"));
        return response()->file(Storage::path($imagePath));
    }

    /**
     * 画像ファイルのアップロード
     *
     * @param DebugFileUploadRequest $request
     * @return JsonResponse
     */
    public function uploadImage(DebugFileUploadRequest $request): JsonResponse
    {
        // アップロードするディレクトリ名を指定
        // $uploadDirectory = '/images/debug/';
        $uploadDirectory = Config::get('myappFile.upload.storage.local.images.debug');

        /** @var UploadedFile $file */
        $file = $request->image;

        // ファイルが存在しない場合
        if (is_null($file)) {
            return response()->json(['message' => 'No file uploaded!', 'status' => 200], 200);
        }

        $fileResource = ImageLibrary::getFileResource($file);

        // DBへの登録処理
        $resource = ImagesResource::toArrayForCreate($fileResource);

        DB::beginTransaction();

        try {
            $insertCount = $this->imagesRepository->create($resource);

            // ファイル名
            $fileName = $fileResource[Images::UUID] . '.' . $fileResource[Images::EXTENTION];
            // ファイルの格納(公開する場合はオプションとして’public’を指定する。)
            // $request->file('image')->storeAs($uploadDirectory, $fileName, 'public');
            // $request->file('image')->storeAs($uploadDirectory, $fileName);

            $result = $file->storeAs($uploadDirectory, $fileName);

            DB::commit();

            // ファイル名
            $fileName = $fileResource[Images::UUID] . '.' . $fileResource[Images::EXTENTION];
            // ファイルの格納(公開する場合はオプションとして’public’を指定する。)
            // $request->file('image')->storeAs($uploadDirectory, $fileName, 'public');
            // $request->file('image')->storeAs($uploadDirectory, $fileName);
            $result = $file->storeAs($uploadDirectory, $fileName);

            // 作成されている場合は304
            $message = ($insertCount > 0 && $result) ? 'success' : 'Bad Request';
            $status = ($insertCount > 0 && $result) ? 201 : 401;

            return response()->json(
                [
                    'message' => $message,
                    'status'  => $status,
                    'data'    => [
                        'uuid'    => $resource[IMAGES::UUID],
                        'ver'     => $resource[IMAGES::VERSION],
                        'query'   => '?uuid=' . $resource[IMAGES::UUID] . '?ver=' . $resource[IMAGES::VERSION],
                    ],
                ],
                $status
            );
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'message: ' . json_encode($e->getMessage()));
            DB::rollback();
            throw $e;
        }
    }
}
