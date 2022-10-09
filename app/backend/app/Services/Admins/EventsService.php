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
use App\Http\Resources\Admins\EventsResource;
use App\Repositories\Admins\Events\EventsRepositoryInterface;
use App\Exports\Masters\Events\EventsExport;
use App\Exports\Masters\Events\EventsBulkInsertTemplateExport;
use App\Imports\Masters\Events\EventsImport;
use App\Library\Array\ArrayLibrary;
use App\Library\Cache\CacheLibrary;
use App\Models\Masters\Events;
use Exception;

class EventsService
{
    // cache keys
    private const CACHE_KEY_EVENT_COIN_COLLECTION_LIST = 'admin_event_collection_list';

    protected EventsRepositoryInterface $eventsRepository;

    /**
     * create service instance
     *
     * @param EventsRepositoryInterface $eventsRepository
     * @return void
     */
    public function __construct(EventsRepositoryInterface $eventsRepository)
    {
        $this->eventsRepository = $eventsRepository;
    }

    /**
     * get event data
     *
     * @param
     * @return JsonResponse
     */
    public function getEvents(): JsonResponse
    {
        $cache = CacheLibrary::getByKey(self::CACHE_KEY_EVENT_COIN_COLLECTION_LIST);

        // キャッシュチェック
        if (is_null($cache)) {
            $collection = $this->eventsRepository->getRecords();
            $resourceCollection = EventsResource::toArrayForGetInformationsCollection($collection);

            if (!empty($resourceCollection)) {
                CacheLibrary::setCache(self::CACHE_KEY_EVENT_COIN_COLLECTION_LIST, $resourceCollection);
            }
        } else {
            $resourceCollection = $cache;
        }

        return response()->json($resourceCollection, 200);
    }

    /**
     * download event data service
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadCSV()
    {
        $data = $this->eventsRepository->getRecords();

        return Excel::download(new EventsExport($data), 'events_info_' . Carbon::now()->format('YmdHis') . '.csv');
    }

    /**
     * download events template data service
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadTemplate()
    {
        return Excel::download(
            new EventsBulkInsertTemplateExport(collect(Config::get('myappFile.service.admins.events.template'))),
            'master_events_template_' . Carbon::now()->format('YmdHis') . '.xlsx'
        );
    }


    /**
     * imort events by template data service
     *
     * @param UploadedFile $file
     * @return JsonResponse
     */
    public function importTemplate(UploadedFile $file)
    {
        // ファイル名チェック
        if (!preg_match('/^master_events_template_\d{14}\.xlsx/u', $file->getClientOriginalName())) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_422,
                'no include title.'
            );
        }

        DB::beginTransaction();
        try {
            // Excel::import(new EnemiesImport, $file, null, \Maatwebsite\Excel\Excel::XLSX);
            // Excel::import(new EnemiesImport($file), $file, null, \Maatwebsite\Excel\Excel::XLSX);
            $fileData = Excel::toArray(new EventsImport($file), $file, null, \Maatwebsite\Excel\Excel::XLSX);

            // $resource = app()->make(GameEnemiesCreateResource::class, ['resource' => $fileData[0]])->toArray($request);
            $resource = EventsResource::toArrayForBulkInsert(current($fileData));

            $insertCount = $this->eventsRepository->create($resource);

            DB::commit();

            // キャッシュの削除
            CacheLibrary::deleteCache(self::CACHE_KEY_EVENT_COIN_COLLECTION_LIST, true);

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
     * create event data service
     *
     * @param string $name name
     * @param int $type type
     * @param string $detail detail
     * @param string $startAt start datetime
     * @param string $endAt end datetime
     * @return \Illuminate\Http\JsonResponse
     */
    public function createEvent(string $name, int $type, string $detail, string $startAt, string $endAt): JsonResponse
    {
        $resource = Events::toArrayForCreate($name, $type, $detail, $startAt, $endAt);

        DB::beginTransaction();
        try {
            $insertCount = $this->eventsRepository->create($resource);

            DB::commit();

            // キャッシュの削除
            CacheLibrary::deleteCache(self::CACHE_KEY_EVENT_COIN_COLLECTION_LIST, true);

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
     * update event data service
     *
     * @param int $id record id
     * @param string $name name
     * @param int $type type
     * @param string $detail detail
     * @param string $startAt start datetime
     * @param string $endAt end datetime
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateEvent(int $id, string $name, int $type, string $detail, string $startAt, string $endAt): JsonResponse
    {
        $resource = Events::toArrayForUpdate($name, $type, $detail, $startAt, $endAt);

        DB::beginTransaction();
        try {
            // ロックをかける為transaction内で実行
            $event = $this->getEventById($id);
            $updatedRowCount = $this->eventsRepository->update($event[Events::ID], $resource);

            DB::commit();

            // キャッシュの削除
            CacheLibrary::deleteCache(self::CACHE_KEY_EVENT_COIN_COLLECTION_LIST, true);

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
     * delete event data service
     *
     * @param array $informationIds id of records
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteEvent(array $informationIds): JsonResponse
    {
        DB::beginTransaction();
        try {
            $resource = Events::toArrayForDelete();

            // ロックをかける為transaction内で実行
            $rows = $this->getEventsByIds($informationIds);

            $deleteRowCount = $this->eventsRepository->delete($informationIds, $resource);

            DB::commit();

            // キャッシュの削除
            CacheLibrary::deleteCache(self::CACHE_KEY_EVENT_COIN_COLLECTION_LIST, true);

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
    private function getEventById(int $coinId): array
    {
        // 更新用途で使う為lockをかける
        $events = $this->eventsRepository->getById($coinId, true);

        if (empty($events)) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_500,
                'not exist coin.'
            );
        }

        // 複数チェックはrepository側で実施済み
        return ArrayLibrary::toArray(ArrayLibrary::getFirst($events->toArray()));
    }

    /**
     * get events by event ids.
     *
     * @param array $ids records id
     * @return array
     */
    private function getEventsByIds(array $ids): array
    {
        // 更新用途で使う為lockをかける
        $events = $this->eventsRepository->getByIds($ids, true);

        if (empty($events)) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_500,
                'not exist events.'
            );
        }

        // 複数チェックはrepository側で実施済み
        return ArrayLibrary::toArray($events->toArray());
    }
}