<?php

declare(strict_types=1);

namespace App\Services\Users;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Http\Resources\Users\EventsResource;
use App\Repositories\Masters\Events\EventsRepositoryInterface;
use App\Library\Array\ArrayLibrary;
use App\Library\Cache\CacheLibrary;
use Exception;

class EventsService
{
    // cache keys
    private const CACHE_KEY_USER_EVENT_LIST = 'cache_user_event_list';

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
        $cache = CacheLibrary::getByKey(self::CACHE_KEY_USER_EVENT_LIST);

        // キャッシュチェック
        if (is_null($cache)) {
            $collection = $this->eventsRepository->getRecords();
            $resourceCollection = EventsResource::toArrayForGetCollectionList($collection);

            if (!empty($resourceCollection)) {
                CacheLibrary::setCache(self::CACHE_KEY_USER_EVENT_LIST, $resourceCollection);
            }
        } else {
            $resourceCollection = (array)$cache;
        }

        return $resourceCollection;
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
