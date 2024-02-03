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
use App\Http\Resources\Admins\EventsResource;
use App\Repositories\Masters\Events\EventsRepositoryInterface;
use App\Exports\Masters\Events\EventsExport;
use App\Exports\Masters\Events\EventsBulkInsertTemplateExport;
use App\Imports\Masters\Events\EventsImport;
use App\Library\Array\ArrayLibrary;
use App\Library\Cache\CacheLibrary;
use App\Library\Time\TimeLibrary;
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
     * @return array
     */
    public function getEvents(): array
    {
        $cache = CacheLibrary::getByKey(self::CACHE_KEY_EVENT_COIN_COLLECTION_LIST);

        // キャッシュチェック
        if (is_null($cache)) {
            $collection = $this->eventsRepository->getRecords();
            $resourceCollection = EventsResource::toArrayForGetCollectionList($collection);

            if (!empty($resourceCollection)) {
                CacheLibrary::setCache(self::CACHE_KEY_EVENT_COIN_COLLECTION_LIST, $resourceCollection);
            }
        } else {
            $resourceCollection = (array)$cache;
        }

        return $resourceCollection;
    }

    /**
     * download event data service
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadCSV()
    {
        $data = $this->eventsRepository->getRecords();

        return Excel::download(new EventsExport($data), 'events_info_' . TimeLibrary::getCurrentDateTime(TimeLibrary::DATE_TIME_FORMAT_YMDHIS) . '.csv');
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
            'master_events_template_' . TimeLibrary::getCurrentDateTime(TimeLibrary::DATE_TIME_FORMAT_YMDHIS) . '.xlsx'
        );
    }


    /**
     * imort events by template data service
     *
     * @param UploadedFile $file
     * @return void
     */
    public function importTemplate(UploadedFile $file): void
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

            $result = $this->eventsRepository->create($resource);

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
            CacheLibrary::deleteCache(self::CACHE_KEY_EVENT_COIN_COLLECTION_LIST, true);

            // return response()->json(['message' => $message, 'status' => $status], $status);
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
     * @return void
     */
    public function createEvent(string $name, int $type, string $detail, string $startAt, string $endAt): void
    {
        $resource = EventsResource::toArrayForCreate($name, $type, $detail, $startAt, $endAt);

        DB::beginTransaction();
        try {
            $result = $this->eventsRepository->create($resource);

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
            CacheLibrary::deleteCache(self::CACHE_KEY_EVENT_COIN_COLLECTION_LIST, true);

            // return response()->json(['message' => $message, 'status' => $status], $status);
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
     * @return void
     */
    public function updateEvent(
        int $id,
        string $name,
        int $type,
        string $detail,
        string $startAt,
        string $endAt
    ): void {
        $resource = EventsResource::toArrayForUpdate($name, $type, $detail, $startAt, $endAt);

        DB::beginTransaction();
        try {
            // ロックをかける為transaction内で実行
            $event = $this->getEventById($id);
            $updatedRowCount = $this->eventsRepository->update($event[Events::ID], $resource);

            // 更新出来ない場合
            if (!($updatedRowCount > 0)) {
                throw new MyApplicationHttpException(
                    StatusCodeMessages::STATUS_401,
                    parameter: [
                        'resource' => $resource,
                        'event.id' => $id,
                    ]
                );
            }

            DB::commit();

            // キャッシュの削除
            CacheLibrary::deleteCache(self::CACHE_KEY_EVENT_COIN_COLLECTION_LIST, true);

            // return response()->json(['message' => $message, 'status' => $status], $status);
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'message: ' . json_encode($e->getMessage()));
            DB::rollback();
            abort(500);
        }
    }

    /**
     * delete event data service
     *
     * @param array $eventIds id of records
     * @return void
     */
    public function deleteEvent(array $eventIds): void
    {
        DB::beginTransaction();
        try {
            $resource = EventsResource::toArrayForDelete();

            // ロックをかける為transaction内で実行
            $rows = $this->getEventsByIds($eventIds);

            $deleteRowCount = $this->eventsRepository->delete($eventIds, $resource);

            // 削除出来ない場合
            if (!($deleteRowCount > 0)) {
                throw new MyApplicationHttpException(
                    StatusCodeMessages::STATUS_401,
                    parameter: [
                        'resource' => $resource,
                        'eventIds' => $eventIds,
                    ]
                );
            }

            DB::commit();

            // キャッシュの削除
            CacheLibrary::deleteCache(self::CACHE_KEY_EVENT_COIN_COLLECTION_LIST, true);

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
