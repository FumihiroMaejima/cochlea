<?php

namespace App\Services\Admins;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Http\Resources\Admins\BannersResource;
use App\Repositories\Admins\Banners\BannersRepositoryInterface;
use App\Exports\Masters\Banners\BannersExport;
use App\Exports\Masters\Banners\BannersBulkInsertTemplateExport;
use App\Imports\Masters\Banners\BannersImport;
use App\Library\Array\ArrayLibrary;
use App\Library\Cache\CacheLibrary;
use App\Library\String\UuidLibrary;
use App\Models\Masters\Banners;
use Exception;

class BannersService
{
    // cache keys
    private const CACHE_KEY_ADMIN_BANNER_COLLECTION_LIST = 'admin_banner_collection_list';

    protected BannersRepositoryInterface $bannersRepository;

    /**
     * create CoinsService instance
     *
     * @param  \App\Repositories\Admins\Banners\BannersRepositoryInterface $bannersRepository
     * @return void
     */
    public function __construct(BannersRepositoryInterface $bannersRepository)
    {
        $this->bannersRepository = $bannersRepository;
    }

    /**
     * get banners data
     *
     * @return JsonResponse
     */
    public function getBanners(): JsonResponse
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
            $resourceCollection = $cache;
        }

        return response()->json($resourceCollection, 200);
    }

    /**
     * download banner data service
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadCSV()
    {
        $data = $this->bannersRepository->getRecords();

        return Excel::download(new BannersExport($data), 'banners_info_' . Carbon::now()->format('YmdHis') . '.csv');
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
            'master_banners_template_' . Carbon::now()->format('YmdHis') . '.xlsx'
        );
    }


    /**
     * imort banners by template data service
     *
     * @param UploadedFile $file
     * @return JsonResponse
     */
    public function importTemplate(UploadedFile $file)
    {
        // ファイル名チェック
        if (!preg_match('/^master_coins_template_\d{14}\.xlsx/u', $file->getClientOriginalName())) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_422,
                'no include title.'
            );
        }

        DB::beginTransaction();
        try {
            $fileData = Excel::toArray(new BannersImport($file), $file, null, \Maatwebsite\Excel\Excel::XLSX);

            $resource = BannersResource::toArrayForBulkInsert(current($fileData));

            $insertCount = $this->bannersRepository->create($resource);

            DB::commit();

            // キャッシュの削除
            CacheLibrary::deleteCache(self::CACHE_KEY_ADMIN_BANNER_COLLECTION_LIST, true);

            // レスポンスの制御
            $message = ($insertCount > 0) ? 'success' : 'Bad Request';
            $status = ($insertCount > 0) ? 201 : 401;

            return response()->json(['message' => $message, 'status' => $status], $status);
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
     * @param int $location location vlaue
     * @param int $pcHeight pc height
     * @param int $pcWidth pc width
     * @param int $spHeight sp height
     * @param int $spWidth sp width
     * @param string $startAt start datetime
     * @param string $endAt end datetime
     * @param string $url url
     * @return \Illuminate\Http\JsonResponse
     */
    public function createBanner(
        string $name,
        string $detail,
        int $location,
        int $pcHeight,
        int $pcWidth,
        int $spHeight,
        int $spWidth,
        string $startAt,
        string $endAt,
        string $url
        ): JsonResponse {
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
            $insertCount = $this->bannersRepository->create($resource);

            DB::commit();

            // キャッシュの削除
            CacheLibrary::deleteCache(self::CACHE_KEY_ADMIN_BANNER_COLLECTION_LIST, true);

            // 作成されている場合は304
            $message = ($insertCount > 0) ? 'success' : 'Bad Request';
            $status = ($insertCount > 0) ? 201 : 401;

            return response()->json(['message' => $message, 'status' => $status], $status);
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
     * @param int $location location vlaue
     * @param int $pcHeight pc height
     * @param int $pcWidth pc width
     * @param int $spHeight sp height
     * @param int $spWidth sp width
     * @param string $startAt start datetime
     * @param string $endAt end datetime
     * @param string $url url
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateBanner(
        string $uuid,
        string $name,
        string $detail,
        int $location,
        int $pcHeight,
        int $pcWidth,
        int $spHeight,
        int $spWidth,
        string $startAt,
        string $endAt,
        string $url
    ): JsonResponse {
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

            DB::commit();

            // キャッシュの削除
            CacheLibrary::deleteCache(self::CACHE_KEY_ADMIN_BANNER_COLLECTION_LIST, true);

            // 更新されていない場合は304
            $message = ($updatedRowCount > 0) ? 'success' : 'not modified';
            $status = ($updatedRowCount > 0) ? 200 : 304;

            return response()->json(['message' => $message, 'status' => $status], $status);
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteBanner(array $bannerUuids): JsonResponse
    {
        DB::beginTransaction();
        try {
            $resource = BannersResource::toArrayForDelete();

            // ロックをかける為transaction内で実行
            $banners = $this->getBannersByUuid($bannerUuids);

            $deleteRowCount = $this->bannersRepository->delete($bannerUuids, $resource);

            DB::commit();

            // キャッシュの削除
            CacheLibrary::deleteCache(self::CACHE_KEY_ADMIN_BANNER_COLLECTION_LIST, true);

            // 更新されていない場合は304
            $message = ($deleteRowCount > 0) ? 'success' : 'not deleted';
            $status = ($deleteRowCount > 0) ? 200 : 401;

            return response()->json(['message' => $message, 'status' => $status], $status);
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'message: ' . json_encode($e->getMessage()));
            DB::rollback();
            abort(500);
        }
    }

    /**
     * get banner by banner uuid.
     *
     * @param int $uuid banner uuid
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
