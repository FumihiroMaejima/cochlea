<?php

declare(strict_types=1);

namespace App\Services\Users;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Http\Resources\Users\InformationsResource;
use App\Http\Resources\Users\UserReadInformationsResource;
use App\Repositories\Masters\Informations\InformationsRepositoryInterface;
use App\Repositories\Users\UserReadInformations\UserReadInformationsRepositoryInterface;
use App\Library\Array\ArrayLibrary;
use App\Library\Cache\CacheLibrary;
use App\Library\Database\TransactionLibrary;
use App\Library\User\UserLibrary;
use Exception;

class InformationsService
{
    // cache keys
    private const CACHE_KEY_USER_INFORMATION_LIST = 'cache_user_information_list';

    protected InformationsRepositoryInterface $informationsRepository;
    protected UserReadInformationsRepositoryInterface $userReadInformationsRepository;

    /**
     * create service instance
     *
     * @param InformationsRepositoryInterface $informationsRepository
     * @param UserReadInformationsRepositoryInterface $userReadInformationsRepository
     * @return void
     */
    public function __construct(
        InformationsRepositoryInterface $informationsRepository,
        UserReadInformationsRepositoryInterface $userReadInformationsRepository
    ) {
        $this->informationsRepository = $informationsRepository;
        $this->userReadInformationsRepository = $userReadInformationsRepository;
    }

    /**
     * get information data
     *
     * @param
     * @return array
     * @throws Exception
     */
    public function getInformations(): array
    {
        $cache = CacheLibrary::getByKey(self::CACHE_KEY_USER_INFORMATION_LIST);

        // キャッシュチェック
        if (is_null($cache)) {
            $collection = $this->informationsRepository->getRecords();
            $resourceCollection = InformationsResource::toArrayForGetTextAndValueList($collection);

            if (!empty($resourceCollection)) {
                CacheLibrary::setCache(self::CACHE_KEY_USER_INFORMATION_LIST, $resourceCollection);
            }
        } else {
            $resourceCollection = (array)$cache;
        }

        return $resourceCollection;
    }

    /**
     * create user rercord.
     *
     * @param int $userId user id
     * @param int $informationId information id.
     * @return void
     */
    public function createUserReadInformation(int $userId, int $informationId): void
    {
        // お知らせの取得
        $information = $this->getInformationById($userId, $informationId);
        // TODO 期間判定
        if (is_null($information)) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_500,
                'Information Not Exist.'
            );
        }

        // DB 登録
        // DB::beginTransaction();
        TransactionLibrary::beginTransactionByUserId($userId);
        try {
            // ロックの実行
            UserLibrary::lockUser($userId);

            // ユーザー情報取得
            $userReadInformation = $this->userReadInformationsRepository->getByUserIdAndInformationId($userId, $informationId);
            if ($userReadInformation) {
                throw new MyApplicationHttpException(
                    StatusCodeMessages::STATUS_500,
                    'User Read Information is Aready Exist.'
                );
            }

            $resource = UserReadInformationsResource::toArrayForCreate($userId, $informationId);
            $result = $this->userReadInformationsRepository->create($userId, $resource);

            if (!$result) {
                throw new MyApplicationHttpException(
                    StatusCodeMessages::STATUS_500,
                    'Create record failed.'
                );
            }

            // ログの設定
            /* $userCoinPaymentLogResource = UserCoinPaymentLogResource::toArrayForCreate(
                $userId,
                $orderId,
                $userCoinPaymentStatus[UserCoinPaymentStatus::COIN_ID],
                UserCoinPaymentStatus::PAYMENT_STATUS_COMPLETE
            );
            $this->userCoinPaymentLogRepository->create($userId, $userCoinPaymentLogResource); */

            // DB::commit();
            TransactionLibrary::commitByUserId($userId);
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'message: ' . json_encode($e->getMessage()));
            // DB::rollback();
            TransactionLibrary::rollbackByUserId($userId);
            throw $e;
        }
    }


    /**
     * remove user rercord.
     *
     * @param int $userId user id
     * @param int $informationId information id.
     * @return void
     */
    public function removeUserReadInformation(int $userId, int $informationId): void
    {
        // お知らせの取得
        $information = $this->getInformationById($userId, $informationId);
        // TODO 期間判定
        if (is_null($information)) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_500,
                'Information Not Exist.'
            );
        }

        // DB 登録
        // DB::beginTransaction();
        TransactionLibrary::beginTransactionByUserId($userId);
        try {
            // ロックの実行
            UserLibrary::lockUser($userId);

            // 既読情報の取得
            $userReadInformation = $this->userReadInformationsRepository->getByUserIdAndInformationId($userId, $informationId);
            if (is_null($userReadInformation)) {
                throw new MyApplicationHttpException(
                    StatusCodeMessages::STATUS_500,
                    'User Read Information is Not Exist.'
                );
            }

            $resource = UserReadInformationsResource::toArrayForDelete($userId, $informationId);
            $removeCount = $this->userReadInformationsRepository->delete($userId, $resource);

            if ($removeCount <= 0) {
                throw new MyApplicationHttpException(
                    StatusCodeMessages::STATUS_500,
                    'Delete record failed.'
                );
            }

            // ログの設定
            /* $userCoinPaymentLogResource = UserCoinPaymentLogResource::toArrayForCreate(
                $userId,
                $orderId,
                $userCoinPaymentStatus[UserCoinPaymentStatus::COIN_ID],
                UserCoinPaymentStatus::PAYMENT_STATUS_COMPLETE
            );
            $this->userCoinPaymentLogRepository->create($userId, $userCoinPaymentLogResource); */

            // DB::commit();
            TransactionLibrary::commitByUserId($userId);
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'message: ' . json_encode($e->getMessage()));
            // DB::rollback();
            TransactionLibrary::rollbackByUserId($userId);
            throw $e;
        }
    }

    /**
     * get resource by rocord id.
     *
     * @param int $informationId information id
     * @return array
     */
    private function getInformationById(int $informationId): array
    {
        // 更新用途で使う為lockをかける
        $informations = $this->informationsRepository->getById($informationId, true);

        if (empty($informations)) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_500,
                'not exist informations.: ' . $informationId,
                ['informationId' => $informationId, 'informations' => $informations],
                true
            );
            /* throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_500,
                'not exist informations.'
            ); */
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
