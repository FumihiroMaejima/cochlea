<?php

declare(strict_types=1);

namespace App\Services\Users;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Http\Resources\Users\UserCoinHistoriesResource;
use App\Repositories\Masters\Coins\CoinsRepositoryInterface;
use App\Repositories\Users\UserCoinHistories\UserCoinHistoriesRepositoryInterface;
use App\Repositories\Users\UserCoins\UserCoinsRepositoryInterface;
use App\Repositories\Users\UserCoins\UserCoinsRepository;
use App\Library\Array\ArrayLibrary;
use App\Library\Cache\CacheLibrary;
use App\Library\File\PdfLibrary;
use App\Models\Masters\Coins;
use App\Models\Users\UserCoinHistories;
use App\Models\Users\UserCoins;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UserCoinHistoryService
{
    // cache keys
    private const CACHE_KEY_USER_COIN_COLLECTION_LIST = 'user_coin_collection_list';

    protected CoinsRepositoryInterface $coinsRepository;
    protected UserCoinHistoriesRepositoryInterface $userCoinHistoriesRepositoryInterface;
    protected UserCoinsRepositoryInterface $userCoinsRepository;

    /**
     * create instance
     *
     * @param CoinsRepositoryInterface $coinsRepository
     * @param UserCoinHistoriesRepositoryInterface $userCoinHistoriesRepositoryInterface
     * @param UserCoinsRepositoryInterface $userCoinsRepository
     * @return void
     */
    public function __construct(
        CoinsRepositoryInterface $coinsRepository,
        UserCoinHistoriesRepositoryInterface $userCoinHistoriesRepositoryInterface,
        UserCoinsRepositoryInterface $userCoinsRepository
    ) {
        $this->coinsRepository = $coinsRepository;
        $this->userCoinHistoriesRepositoryInterface = $userCoinHistoriesRepositoryInterface;
        $this->userCoinsRepository = $userCoinsRepository;
    }

    /**
     * get user coin history list.
     *
     * @param int $userId user id
     * @return JsonResponse
     */
    public function getCoinHistory(int $userId): JsonResponse
    {
        $coinHistories = $this->userCoinHistoriesRepositoryInterface->getListByUserId($userId);
        $coinHistoryResources = UserCoinHistoriesResource::toArrayForList(
            UserCoinHistories::sortByUpdatedAt(ArrayLibrary::toArray($coinHistories->toArray()), SORT_DESC)
        );

        return response()->json(
            [
                'code' => 200,
                'message' => 'Success',
                'data' => $coinHistoryResources,
            ]
        );
    }

    /**
     * get user coin history by uuid.
     *
     * @param int $userId user id
     * @param string $uuid uuid
     * @return JsonResponse
     */
    public function getCoinHistoryByUuid(int $userId, string $uuid): JsonResponse
    {
        $coinHistory = $this->userCoinHistoriesRepositoryInterface->getByUserIdAndUuId($userId, $uuid);

        if (is_null($coinHistory)) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_404,
                'not exitst coin history.'
            );
        }

        $coinHistoryResource = UserCoinHistoriesResource::toArrayForSingleRecord(
            ArrayLibrary::getFirst(ArrayLibrary::toArray($coinHistory->toArray()))
        );

        return response()->json(
            [
                'code' => 200,
                'message' => 'Success',
                'data' => $coinHistoryResource,
            ]
        );
    }

    /**
     * get user coin history pdf by uuid.
     *
     * @param int $userId user id
     * @param string $uuid uuid
     * @return BinaryFileResponse
     */
    public function getCoinHistoryPdfByUuid(int $userId, string $uuid): BinaryFileResponse
    {
        $coinHistory = $this->userCoinHistoriesRepositoryInterface->getByUserIdAndUuId($userId, $uuid);

        if (is_null($coinHistory)) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_404,
                'not exitst coin history.'
            );
        }

        $coinHistoryResource = UserCoinHistoriesResource::toArrayForSingleRecord(
            ArrayLibrary::getFirst(ArrayLibrary::toArray($coinHistory->toArray()))
        );

        $file = PdfLibrary::getSamplePDF();

        return response()->file($file);
    }

    /**
     * get user coins by user id.
     *
     * @param int $userId user id
     * @param bool $isLock exec lock For Update
     * @return array|null
     */
    private function getUserCoinByUserId(int $userId, bool $isLock = false): array|null
    {
        $userCoin = $this->userCoinsRepository->getByUserId($userId, $isLock);

        if (is_null($userCoin)) {
            return $userCoin;
        }

        // 複数チェックはrepository側で実施済み
        return ArrayLibrary::toArray(ArrayLibrary::getFirst($userCoin->toArray()));
    }
}
