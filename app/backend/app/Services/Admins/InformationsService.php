<?php

declare(strict_types=1);

namespace App\Services\Admins;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Http\Resources\Admins\InformationsResource;
use App\Repositories\Masters\Informations\InformationsRepositoryInterface;
use App\Exports\Masters\Informations\InformationsExport;
use App\Exports\Masters\Informations\InformationsBulkInsertTemplateExport;
use App\Imports\Masters\Informations\InformationsImport;
use App\Library\Array\ArrayLibrary;
use App\Library\Cache\CacheLibrary;
use App\Library\Time\TimeLibrary;
use App\Models\Masters\Informations;
use Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class InformationsService
{
    // cache keys
    private const CACHE_KEY_INFORMATION_COIN_COLLECTION_LIST = 'admin_information_collection_list';

    protected InformationsRepositoryInterface $informationsRepository;

    /**
     * create service instance
     *
     * @param InformationsRepositoryInterface $informationsRepository
     * @return void
     */
    public function __construct(InformationsRepositoryInterface $informationsRepository)
    {
        $this->informationsRepository = $informationsRepository;
    }

    /**
     * get information data
     *
     * @param
     * @return array
     */
    public function getInformations(): array
    {
        $cache = CacheLibrary::getByKey(self::CACHE_KEY_INFORMATION_COIN_COLLECTION_LIST);

        // キャッシュチェック
        if (is_null($cache)) {
            $collection = $this->informationsRepository->getRecords();
            $resourceCollection = InformationsResource::toArrayForGetInformationsCollection($collection);

            if (!empty($resourceCollection)) {
                CacheLibrary::setCache(self::CACHE_KEY_INFORMATION_COIN_COLLECTION_LIST, $resourceCollection);
            }
        } else {
            $resourceCollection = (array)$cache;
        }

        return $resourceCollection;
    }

    /**
     * download information data service
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadCSV(): BinaryFileResponse
    {
        $data = $this->informationsRepository->getRecords();

        return Excel::download(new InformationsExport($data), 'informations_info_' . TimeLibrary::getCurrentDateTime(TimeLibrary::DATE_TIME_FORMAT_YMDHIS) . '.csv');
    }

    /**
     * download informations template data service
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadTemplate(): BinaryFileResponse
    {
        return Excel::download(
            new InformationsBulkInsertTemplateExport(collect(Config::get('myappFile.service.admins.informations.template'))),
            'master_informations_template_' . TimeLibrary::getCurrentDateTime(TimeLibrary::DATE_TIME_FORMAT_YMDHIS) . '.xlsx'
        );
    }


    /**
     * imort informations by template data service
     *
     * @param UploadedFile $file
     * @return void
     */
    public function importTemplate(UploadedFile $file): void
    {
        // ファイル名チェック
        if (!preg_match('/^master_informations_template_\d{14}\.xlsx/u', $file->getClientOriginalName())) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_422,
                'no include title.'
            );
        }

        DB::beginTransaction();
        try {
            // Excel::import(new EnemiesImport, $file, null, \Maatwebsite\Excel\Excel::XLSX);
            // Excel::import(new EnemiesImport($file), $file, null, \Maatwebsite\Excel\Excel::XLSX);
            $fileData = Excel::toArray(new InformationsImport($file), $file, null, \Maatwebsite\Excel\Excel::XLSX);

            // $resource = app()->make(GameEnemiesCreateResource::class, ['resource' => $fileData[0]])->toArray($request);
            $resource = InformationsResource::toArrayForBulkInsert(current($fileData));

            $result = $this->informationsRepository->create($resource);

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
            CacheLibrary::deleteCache(self::CACHE_KEY_INFORMATION_COIN_COLLECTION_LIST, true);

            // return response()->json(['message' => $message, 'status' => $status], $status);
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'message: ' . json_encode($e->getMessage()));
            DB::rollback();
            abort(500);
        }
    }

    /**
     * create information data service
     *
     * @param string $name name
     * @param int $type type
     * @param string $detail detail
     * @param string $startAt start datetime
     * @param string $endAt end datetime
     * @return void
     */
    public function createInformation(
        string $name,
        int $type,
        string $detail,
        string $startAt,
        string $endAt
    ): void {
        $resource = InformationsResource::toArrayForCreate($name, $type, $detail, $startAt, $endAt);

        DB::beginTransaction();
        try {
            $result = $this->informationsRepository->create($resource);

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
            CacheLibrary::deleteCache(self::CACHE_KEY_INFORMATION_COIN_COLLECTION_LIST, true);

            // return response()->json(['message' => $message, 'status' => $status], $status);
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'message: ' . json_encode($e->getMessage()));
            DB::rollback();
            abort(500);
        }
    }

    /**
     * update information data service
     *
     * @param int $id record id
     * @param string $name name
     * @param int $type type
     * @param string $detail detail
     * @param string $startAt start datetime
     * @param string $endAt end datetime
     * @return void
     */
    public function updateInformation(
        int $id,
        string $name,
        int $type,
        string $detail,
        string $startAt,
        string $endAt
        ): void {
        $resource = InformationsResource::toArrayForUpdate($name, $type, $detail, $startAt, $endAt);

        DB::beginTransaction();
        try {
            // ロックをかける為transaction内で実行
            $information = $this->getInformationById($id);
            $updatedRowCount = $this->informationsRepository->update($information[Informations::ID], $resource);

            // 更新出来ない場合
            if (!($updatedRowCount > 0)) {
                throw new MyApplicationHttpException(
                    StatusCodeMessages::STATUS_401,
                    parameter: [
                        'resource' => $resource,
                        'information.id' => $id,
                    ]
                );
            }

            DB::commit();

            // キャッシュの削除
            CacheLibrary::deleteCache(self::CACHE_KEY_INFORMATION_COIN_COLLECTION_LIST, true);

            // return response()->json(['message' => $message, 'status' => $status], $status);
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'message: ' . json_encode($e->getMessage()));
            DB::rollback();
            abort(500);
        }
    }

    /**
     * delete information data service
     *
     * @param array $informationIds id of records
     * @return void
     */
    public function deleteInformation(array $informationIds): void
    {
        DB::beginTransaction();
        try {
            $resource = InformationsResource::toArrayForDelete();

            // ロックをかける為transaction内で実行
            $rows = $this->getInformationsByIds($informationIds);

            $deleteRowCount = $this->informationsRepository->delete($informationIds, $resource);

            // 削除出来ない場合
            if (!($deleteRowCount > 0)) {
                throw new MyApplicationHttpException(
                    StatusCodeMessages::STATUS_401,
                    parameter: [
                        'resource' => $resource,
                        'informationIds' => $informationIds,
                    ]
                );
            }

            DB::commit();

            // キャッシュの削除
            CacheLibrary::deleteCache(self::CACHE_KEY_INFORMATION_COIN_COLLECTION_LIST, true);

            // return response()->json(['message' => $message, 'status' => $status], $status);
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'message: ' . json_encode($e->getMessage()));
            DB::rollback();
            abort(500);
        }
    }

    /**
     * get resource by rocord id.
     *
     * @param int $coinId coin id
     * @return array
     */
    private function getInformationById(int $coinId): array
    {
        // 更新用途で使う為lockをかける
        $informations = $this->informationsRepository->getById($coinId, true);

        if (empty($informations)) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_500,
                'not exist coin.'
            );
        }

        // 複数チェックはrepository側で実施済み
        return ArrayLibrary::toArray(ArrayLibrary::getFirst($informations->toArray()));
    }

    /**
     * get informations by information ids.
     *
     * @param array $ids records id
     * @return array
     */
    private function getInformationsByIds(array $ids): array
    {
        // 更新用途で使う為lockをかける
        $informations = $this->informationsRepository->getByIds($ids, true);

        if (empty($informations)) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_500,
                'not exist informations.'
            );
        }

        // 複数チェックはrepository側で実施済み
        return ArrayLibrary::toArray($informations->toArray());
    }
}
