<?php

namespace App\Services\Users;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Repositories\Masters\ServiceTerms\ServiceTermsRepositoryInterface;
use App\Repositories\Users\UserServiceTerms\UserServiceTermsRepositoryInterface;
use App\Http\Resources\Users\UserServiceTermsResource;
use App\Library\Array\ArrayLibrary;
use App\Library\Cache\MasterCacheLibrary;
use App\Models\Masters\ServiceTerms;
use Exception;

class ServiceTermsService
{
    protected ServiceTermsRepositoryInterface $serviceTermsRepository;
    protected UserServiceTermsRepositoryInterface $userServiceTermsRepository;

    /**
     * create service instance
     *
     * @param ServiceTermsRepositoryInterface $serviceTermsRepository
     * @param UserServiceTermsRepositoryInterface $userServiceTermsRepository
     * @return void
     */
    public function __construct(
        ServiceTermsRepositoryInterface $serviceTermsRepository,
        UserServiceTermsRepositoryInterface $userServiceTermsRepository
    ) {
        $this->serviceTermsRepository = $serviceTermsRepository;
        $this->userServiceTermsRepository = $userServiceTermsRepository;
    }

    /**
     * get latest service terms
     *
     * @return JsonResponse
     */
    public function getLatestServiceTerms(): JsonResponse
    {
        $cache = MasterCacheLibrary::getServiceTermsCache();

        // キャッシュチェック
        if (is_null($cache)) {
            $collection = $this->serviceTermsRepository->getRecords();
            if (empty($collection)) {
                return [];
            }
            $records = ArrayLibrary::toArray($collection->toArray());

            if (!empty($records)) {
                MasterCacheLibrary::setServiceTermsCache($records);
            }
        } else {
            $records = $cache;
        }
        $serviceTermList = ServiceTerms::sortByVersion($records, SORT_DESC);

        return response()->json(['data' =>current($serviceTermList)]);
    }

    /**
     * create user rercord.
     *
     * @param int $userId user id
     * @param string $serviceTermId service term id.
     * @return JsonResponse
     */
    public function createUserServiceTerm(int $userId, int $serviceTermId): JsonResponse
    {
        // お知らせの取得
        $information = $this->getServiceTermById($userId, $serviceTermId);
        // TODO 期間判定
        // TODO 公開中の最新のバージョンのみ登録出来るようにする
        if (is_null($information)) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_500,
                'Service Term Not Exist.'
            );
        }

        $userServiceterm = $this->userServiceTermsRepository->getByUserIdAndServiceTermId($userId, $serviceTermId);
        if ($userServiceterm) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_500,
                'User Service Term is Aready Exist.'
            );
        }

        // DB 登録
        DB::beginTransaction();
        try {
            $resource = UserServiceTermsResource::toArrayForCreate($userId, $serviceTermId);
            $createCount = $this->userServiceTermsRepository->create($userId, $resource);

            if ($createCount <= 0) {
                throw new MyApplicationHttpException(
                    StatusCodeMessages::STATUS_500,
                    'Create record failed.'
                );
            }

            // ログの設定

            DB::commit();
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'message: ' . json_encode($e->getMessage()));
            DB::rollback();
            throw $e;
        }

        return response()->json(
            [
                'code' => StatusCodeMessages::STATUS_201,
                'message' => 'Successfully Create!',
                'data' => true,
            ],
            StatusCodeMessages::STATUS_201
        );
    }

    /**
     * get resource by rocord id.
     *
     * @param int $serviceTermId service term id
     * @return array
     */
    private function getServiceTermById(int $serviceTermId): array
    {
        // 更新用途で使う為lockをかける
        $serviceTerms = $this->serviceTermsRepository->getById($serviceTermId, true);

        if (empty($serviceTerms)) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_500,
                'not exist serviceTerm.: ' . $serviceTermId,
                ['serviceTermId' => $serviceTermId, 'serviceTerm' => $serviceTerms],
                true
            );
        }

        // 複数チェックはrepository側で実施済み
        return ArrayLibrary::toArray(ArrayLibrary::getFirst($serviceTerms->toArray()));
    }
}
