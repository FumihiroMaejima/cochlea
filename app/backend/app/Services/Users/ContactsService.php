<?php

namespace App\Services\Users;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
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
     * create contact data
     *
     * @param int $userId user id
     * @param string $email email
     * @param string $name name
     * @param int $type type
     * @param string $detail detail
     * @param ?string $failureDetail failure detail
     * @param ?string $failureAt failure datetime
     * @return JsonResponse
     * @throws Exception
     */
    public function createContact(
        int $userId,
        string $email,
        ?string $name,
        int $type,
        string $detail,
        ?string $failureDetail,
        ?string $failureAt
    ): JsonResponse {

        // TODO bodyの連投チェック
        $resource = ContactsResource::toArrayForCreate(
            $userId,
            $email,
            $name,
            $type,
            $detail,
            $failureDetail,
            $failureAt
        );

        DB::beginTransaction();
        try {
            $this->contactsRepository->create($resource);

            // ユーザーへメール送信
            // (new AuthCodeNotificationService($email))->send((string)$code, $expiredAt);

            // 管理者へslack通知
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_401,
                '認証コード生成処理に失敗しました。' . $e->getMessage(),
                [],
                false
            );
        }

        return response()->json(['data' => true], StatusCodeMessages::STATUS_200);
    }
}
