<?php

namespace App\Services\Users;

use Illuminate\Support\Facades\Config;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Http\Resources\Users\CoinsResource;
use App\Http\Resources\Users\ContactsResource;
use App\Repositories\Masters\Contacts\ContactsRepositoryInterface;
use App\Library\Array\ArrayLibrary;
use App\Library\Cache\CacheLibrary;
// use App\Library\Cache\HashCacheLibrary;
use Exception;

class ContactsService
{
    // cache keys
    private const CACHE_KEY_USER_CONTACTS_LIST = 'cache_user_coins_list';

    protected ContactsRepositoryInterface $contactsRepository;

    /**
     * create CoinsService instance
     *
     * @param \App\Repositories\Masters\Contacts\ContactsRepositoryInterface $contactsRepository
     * @return void
     */
    public function __construct(ContactsRepositoryInterface $contactsRepository)
    {
        $this->contactsRepository = $contactsRepository;
    }

    /**
     * get contact categories
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function getCategories(): JsonResponse
    {
        $resource = ContactsResource::toArrayForGetTextAndValueListForCategories();
        return response()->json(['data' => $resource], StatusCodeMessages::STATUS_200);
    }

    /**
     * get coins data
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function getCoins(): JsonResponse
    {
        $cache = CacheLibrary::getByKey(self::CACHE_KEY_USER_CONTACTS_LIST);
        // hash型の検証
        // $testCache = HashCacheLibrary::getByKey(self::CACHE_KEY_USER_CONTACTS_LIST.'_test');

        // キャッシュチェック
        if (is_null($cache)) {
            $collection = $this->contactsRepository->getRecords();
            $resourceCollection = CoinsResource::toArrayForGetTextAndValueList($collection);

            if (!empty($resourceCollection)) {
                CacheLibrary::setCache(self::CACHE_KEY_USER_CONTACTS_LIST, $resourceCollection);
                // HashCacheLibrary::setCache(self::CACHE_KEY_USER_CONTACTS_LIST.'_test', $resourceCollection);
            }
        } else {
            $resourceCollection = $cache;
        }

        return response()->json(['data' => $resourceCollection], StatusCodeMessages::STATUS_200);
    }
}
