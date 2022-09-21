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
use App\Http\Requests\Admin\Informations\InformationCreateRequest;
use App\Http\Requests\Admin\Informations\InformationDeleteRequest;
use App\Http\Requests\Admin\Informations\InformationImportRequest;
use App\Http\Requests\Admin\Informations\InformationUpdateRequest;
use App\Http\Resources\Admins\InformationsResource;
use App\Repositories\Admins\Informations\InformationsRepositoryInterface;
use App\Exports\Masters\Informations\InformationsExport;
use App\Exports\Masters\Informations\InformationsBulkInsertTemplateExport;
use App\Imports\Masters\Informations\InformationsImport;
use App\Library\Array\ArrayLibrary;
use App\Library\Cache\CacheLibrary;
use App\Models\Masters\Informations;
use Exception;

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
     * @return JsonResponse
     */
    public function getInformations(): JsonResponse
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
            $resourceCollection = $cache;
        }

        return response()->json($resourceCollection, 200);
    }

    /**
     * download information data service
     *
     * @param  \Illuminate\Http\Request;  $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadCSV(Request $request)
    {
        $data = $this->informationsRepository->getRecords();

        return Excel::download(new InformationsExport($data), 'coins_info_' . Carbon::now()->format('YmdHis') . '.csv');
    }

    /**
     * download informations template data service
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadTemplate()
    {
        return Excel::download(
            new InformationsBulkInsertTemplateExport(collect(Config::get('myappFile.service.admins.informations.template'))),
            'master_informations_template_' . Carbon::now()->format('YmdHis') . '.xlsx'
        );
    }


    /**
     * imort informations by template data service
     *
     * @param UploadedFile $file
     * @return JsonResponse
     */
    public function importTemplate(UploadedFile $file)
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

            $insertCount = $this->informationsRepository->create($resource);

            DB::commit();

            // キャッシュの削除
            CacheLibrary::deleteCache(self::CACHE_KEY_INFORMATION_COIN_COLLECTION_LIST, true);

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
     * update information data service
     *
     * @param  InformationCreateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function createInformation(InformationCreateRequest $request): JsonResponse
    {
        $resource = InformationsResource::toArrayForCreate($request);

        DB::beginTransaction();
        try {
            $insertCount = $this->informationsRepository->create($resource);

            DB::commit();

            // キャッシュの削除
            CacheLibrary::deleteCache(self::CACHE_KEY_INFORMATION_COIN_COLLECTION_LIST, true);

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
     * update information data service
     *
     * @param  InformationUpdateRequest $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateInformation(InformationUpdateRequest $request, int $id): JsonResponse
    {
        $resource = InformationsResource::toArrayForUpdate($request);

        DB::beginTransaction();
        try {
            // ロックをかける為transaction内で実行
            $information = $this->getInformationById($id);
            $updatedRowCount = $this->informationsRepository->update($information[Informations::ID], $resource);

            DB::commit();

            // キャッシュの削除
            CacheLibrary::deleteCache(self::CACHE_KEY_INFORMATION_COIN_COLLECTION_LIST, true);

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
     * delete information data service
     *
     * @param  InformationDeleteRequest $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteInformation(InformationDeleteRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $informationIds = $request->informations;

            $resource = InformationsResource::toArrayForDelete();

            // ロックをかける為transaction内で実行
            $rows = $this->getInformationsByIds($informationIds);

            $deleteRowCount = $this->informationsRepository->delete($informationIds, $resource);

            DB::commit();

            // キャッシュの削除
            CacheLibrary::deleteCache(self::CACHE_KEY_INFORMATION_COIN_COLLECTION_LIST, true);

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
     * get resource by rocord id.
     *
     * @param int $coinId coin id
     * @return array
     */
    private function getInformationById(int $coinId): array
    {
        // 更新用途で使う為lockをかける
        $coins = $this->informationsRepository->getById($coinId, true);

        if (empty($coins)) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_500,
                'not exist coin.'
            );
        }

        // 複数チェックはrepository側で実施済み
        return ArrayLibrary::toArray(ArrayLibrary::getFirst($coins->toArray()));
    }

    /**
     * get informations by role ids.
     *
     * @param array $coinIds role id
     * @return array
     */
    private function getInformationsByIds(array $coinIds): array
    {
        // 更新用途で使う為lockをかける
        $informations = $this->informationsRepository->getByIds($coinIds, true);

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
