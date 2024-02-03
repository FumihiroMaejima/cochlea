<?php

declare(strict_types=1);

namespace App\Services\Admins;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Http\Resources\Admins\BannersResource;
use App\Repositories\Masters\Banners\BannersRepositoryInterface;
use App\Exports\Masters\Banners\BannersExport;
use App\Exports\Masters\Banners\BannersBulkInsertTemplateExport;
use App\Imports\Masters\Banners\BannersImport;
use App\Library\Array\ArrayLibrary;
use App\Library\Banner\BannerLibrary;
use App\Library\Cache\CacheLibrary;
use App\Library\String\UuidLibrary;
use App\Library\File\FileLibrary;
use App\Library\File\ImageLibrary;
use App\Library\Time\TimeLibrary;
use App\Models\Masters\Banners;
use \Symfony\Component\HttpFoundation\BinaryFileResponse;
use Exception;

class BannersService
{
    // cache keys
    private const CACHE_KEY_ADMIN_BANNER_COLLECTION_LIST = 'admin_banner_collection_list';

    protected BannersRepositoryInterface $bannersRepository;

    /**
     * create CoinsService instance
     *
     * @param  \App\Repositories\Masters\Banners\BannersRepositoryInterface $bannersRepository
     * @return void
     */
    public function __construct(BannersRepositoryInterface $bannersRepository)
    {
        $this->bannersRepository = $bannersRepository;
    }

    /**
     * get banners data
     *
     * @return array
     */
    public function getBanners(): array
    {
        $cache = CacheLibrary::getByKey(self::CACHE_KEY_ADMIN_BANNER_COLLECTION_LIST);

        // キャッシュチェック
        if (is_null($cache)) {
            $collection = $this->bannersRepository->getRecords();
            $resourceCollection = BannersResource::toArrayForGetCollection($collection);

            if (!empty($resourceCollection)) {
                CacheLibrary::setCache(self::CACHE_KEY_ADMIN_BANNER_COLLECTION_LIST, $resourceCollection);
            }
        } else {
            $resourceCollection = (array)$cache;
        }

        return $resourceCollection;
    }

    /**
     * 画像ファイルのダウンロード
     *
     * @param string $uuid
     * @return string
     * @throws MyApplicationHttpException
     */
    public function getImage(string $uuid): string
    {
        $banners = $this->bannersRepository->getByUuid($uuid, true);

        if (empty($banners)) {
            return BannerLibrary::getDefaultBannerStoragePath();
        }

        // 複数チェックはrepository側で実施済み
        $banner = ArrayLibrary::toArray(ArrayLibrary::getFirst($banners->toArray()));
        $bannerId = !empty($banner) ? $banner[Banners::ID] : 0;

        $imagePath = BannerLibrary::getBannerStoragePathByBannerIdAndUuid($bannerId, $uuid, 'png');

        $file = FileLibrary::getFileStoream($imagePath);

        if (is_null($file)) {
            return BannerLibrary::getDefaultBannerStoragePath();
        }

        return Storage::path($imagePath);
    }

    /**
     * download banner data service
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadCSV()
    {
        $data = $this->bannersRepository->getRecords();

        return Excel::download(new BannersExport($data), 'banners_info_' . TimeLibrary::getCurrentDateTime(TimeLibrary::DATE_TIME_FORMAT_YMDHIS) . '.csv');
    }

    /**
     * download banner template data service
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadTemplate()
    {
        return Excel::download(
            new BannersBulkInsertTemplateExport(collect(Config::get('myappFile.service.admins.banners.template'))),
            'master_banners_template_' . TimeLibrary::getCurrentDateTime(TimeLibrary::DATE_TIME_FORMAT_YMDHIS) . '.xlsx'
        );
    }


    /**
     * imort banners by template data service
     *
     * @param UploadedFile $file
     * @return void
     */
    public function importTemplate(UploadedFile $file): void
    {
        // ファイル名チェック
        if (!preg_match('/^master_banners_template_\d{14}\.xlsx/u', $file->getClientOriginalName())) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_422,
                'no valiable file title.',
                isResponseMessage: true
            );
        }

        DB::beginTransaction();
        try {
            $fileData = Excel::toArray(new BannersImport($file), $file, null, \Maatwebsite\Excel\Excel::XLSX);

            $resource = BannersResource::toArrayForBulkInsert(current($fileData));

            $result = $this->bannersRepository->create($resource);

            // 作成出来ない場合
            if (!$result) {
                throw new MyApplicationHttpException(
                    StatusCodeMessages::STATUS_401,
                    parameter: [
                        'resource' => $resource,
                    ]
                );
            }

            DB::commit();

            // キャッシュの削除
            CacheLibrary::deleteCache(self::CACHE_KEY_ADMIN_BANNER_COLLECTION_LIST, true);
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'message: ' . json_encode($e->getMessage()));
            DB::rollback();
            abort(500);
        }
    }

    /**
     * create banner data service
     *
     * @param string $name name
     * @param string $detail detail
     * @param string $location location vlaue
     * @param int $pcHeight pc height
     * @param int $pcWidth pc width
     * @param int $spHeight sp height
     * @param int $spWidth sp width
     * @param string $startAt start datetime
     * @param string $endAt end datetime
     * @param string $url url
     * @param UploadedFile|null $image image file
     * @return void
     */
    public function createBanner(
        string $name,
        string $detail,
        string $location,
        int $pcHeight,
        int $pcWidth,
        int $spHeight,
        int $spWidth,
        string $startAt,
        string $endAt,
        string $url,
        ?UploadedFile $image
    ): void {
        $resource = BannersResource::toArrayForCreate(
            UuidLibrary::uuidVersion4(),
            $name,
            $detail,
            $location,
            $pcHeight,
            $pcWidth,
            $spHeight,
            $spWidth,
            $startAt,
            $endAt,
            $url
        );

        DB::beginTransaction();
        try {
            $result = $this->bannersRepository->create($resource);

            // 作成出来ない場合
            if (!$result) {
                throw new MyApplicationHttpException(
                    StatusCodeMessages::STATUS_401,
                    parameter: [
                        'resource' => $resource,
                    ]
                );
            }

            DB::commit();

            // キャッシュの削除
            CacheLibrary::deleteCache(self::CACHE_KEY_ADMIN_BANNER_COLLECTION_LIST, true);
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'message: ' . json_encode($e->getMessage()));
            DB::rollback();
            abort(500);
        }
    }

    /**
     * update banner data service
     *
     * @param string $uuid uuid
     * @param string $name name
     * @param string $detail detail
     * @param string $location location vlaue
     * @param int $pcHeight pc height
     * @param int $pcWidth pc width
     * @param int $spHeight sp height
     * @param int $spWidth sp width
     * @param string $startAt start datetime
     * @param string $endAt end datetime
     * @param string $url url
     * @param UploadedFile|null $image image file
     * @return void
     */
    public function updateBanner(
        string $uuid,
        string $name,
        string $detail,
        string $location,
        int $pcHeight,
        int $pcWidth,
        int $spHeight,
        int $spWidth,
        string $startAt,
        string $endAt,
        string $url,
        ?UploadedFile $image
    ): void {
        $resource = BannersResource::toArrayForUpdate(
            $uuid,
            $name,
            $detail,
            $location,
            $pcHeight,
            $pcWidth,
            $spHeight,
            $spWidth,
            $startAt,
            $endAt,
            $url
        );

        DB::beginTransaction();
        try {
            // ロックをかける為transaction内で実行
            $banner = $this->getBannerByUuid($uuid);
            $updatedRowCount = $this->bannersRepository->update($banner[Banners::ID], $resource);

            // 更新出来ない場合
            if (!($updatedRowCount > 0)) {
                throw new MyApplicationHttpException(
                    StatusCodeMessages::STATUS_401,
                    parameter: [
                        'resource' => $resource,
                        'banner.id' => $banner[Banners::ID],
                    ]
                );
            }

            // 画像がアップロードされている場合
            if ($image) {
                // アップロードするディレクトリ名を指定
                $directory = BannerLibrary::getBannerStorageDirctory();
                $bannerId = $banner[Banners::ID];

                $fileResource = ImageLibrary::getFileResource($image);
                // ファイル名
                $storageFileName = $fileResource[ImageLibrary::RESOURCE_KEY_NAME] . '.' . $fileResource[ImageLibrary::RESOURCE_KEY_EXTENTION];

                $result = $image->storeAs("$directory$bannerId/", $storageFileName, FileLibrary::getStorageDiskByEnv());
                if (!$result) {
                    throw new MyApplicationHttpException(
                        StatusCodeMessages::STATUS_500,
                        'store file failed.'
                    );
                }
            }

            DB::commit();

            // キャッシュの削除
            CacheLibrary::deleteCache(self::CACHE_KEY_ADMIN_BANNER_COLLECTION_LIST, true);

            // return response()->json(['message' => $message, 'status' => $status], $status);
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'message: ' . json_encode($e->getMessage()));
            DB::rollback();
            abort(500);
        }
    }

    /**
     * delete banner data service
     *
     * @param array<int, string> $bannerUuids
     * @return void
     */
    public function deleteBanner(array $bannerUuids): void
    {
        DB::beginTransaction();
        try {
            $resource = BannersResource::toArrayForDelete();

            // ロックをかける為transaction内で実行
            $banners = $this->getBannersByUuid($bannerUuids);

            $deleteRowCount = $this->bannersRepository->delete(array_column($banners, Banners::ID), $resource);

            // 削除出来ない場合
            if (!($deleteRowCount > 0)) {
                throw new MyApplicationHttpException(
                    StatusCodeMessages::STATUS_401,
                    parameter: [
                        'resource' => $resource,
                        'bannerUuids' => $bannerUuids,
                    ]
                );
            }

            DB::commit();

            // キャッシュの削除
            CacheLibrary::deleteCache(self::CACHE_KEY_ADMIN_BANNER_COLLECTION_LIST, true);

            // return response()->json(['message' => $message, 'status' => $status], $status);
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'message: ' . json_encode($e->getMessage()));
            DB::rollback();
            abort(500);
        }
    }

    /**
     * 画像ファイルのアップロード
     *
     * @param string $uuid
     * @param UploadedFile $image image file
     * @return void
     */
    public function uploadImage(string $uuid, UploadedFile $image): void
    {
        DB::beginTransaction();
        try {
            // ロックをかける為transaction内で実行
            $banner = $this->getBannerByUuid($uuid);

            // 画像の格納
            // アップロードするディレクトリ名を指定
            $directory = BannerLibrary::getBannerStorageDirctory();
            $bannerId = $banner[Banners::ID];

            $fileResource = ImageLibrary::getFileResource($image);
            // ファイル名(UUID)
            $storageFileName = $uuid . '.' . $fileResource[ImageLibrary::RESOURCE_KEY_EXTENTION];

            $result = $image->storeAs("$directory$bannerId/", $storageFileName, FileLibrary::getStorageDiskByEnv());
            if (!$result) {
                throw new MyApplicationHttpException(
                    StatusCodeMessages::STATUS_500,
                    'store file failed.',
                    [
                        'fileResource' => $fileResource,
                        'bannerId' => $bannerId,
                        'storageFileName' => $storageFileName,
                    ]
                );
            }

            // 更新日時の更新
            $resource = BannersResource::toArrayForUpdateImage();
            $updatedRowCount = $this->bannersRepository->update($banner[Banners::ID], $resource);

            // 更新出来ない場合
            // 更新されていない場合は304を返すでも良さそう
            if (!($updatedRowCount > 0)) {
                throw new MyApplicationHttpException(
                    StatusCodeMessages::STATUS_401,
                    parameter: [
                        'resource' => $resource,
                    ]
                );
            }

            DB::commit();

            // キャッシュの削除
            CacheLibrary::deleteCache(self::CACHE_KEY_ADMIN_BANNER_COLLECTION_LIST, true);
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'message: ' . json_encode($e->getMessage()));
            DB::rollback();
            abort(500);
        }
    }

    /**
     * get banner by banner uuid.
     *
     * @param string $uuid banner uuid
     * @return array
     */
    private function getBannerByUuid(string $uuid): array
    {
        // 更新用途で使う為lockをかける
        $banners = $this->bannersRepository->getByUuid($uuid, true);

        if (empty($banners)) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_500,
                'not exist banner.'
            );
        }

        // 複数チェックはrepository側で実施済み
        return ArrayLibrary::toArray(ArrayLibrary::getFirst($banners->toArray()));
    }

    /**
     * get banners by banner uuid list.
     *
     * @param array $uuids banner uuid list
     * @return array
     */
    private function getBannersByUuid(array $uuids): array
    {
        // 更新用途で使う為lockをかける
        $banners = $this->bannersRepository->getByUuids($uuids, true);

        if (empty($banners)) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_500,
                'not exist banners.'
            );
        }

        $banners = ArrayLibrary::toArray($banners->toArray());

        // 指定した件数分取得出来ていない場合
        if (count($banners) !== count($uuids)) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_500,
                'not exist banners.',
                [
                    'uuids' => $uuids,
                ]
            );
        }

        return $banners;
    }
}
