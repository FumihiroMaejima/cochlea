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
        $serviceTermList = $this->getServiceTermList();
        return response()->json(['data' => current($serviceTermList)]);
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
        // 利用規約の取得
        $serviceTermList = $this->getServiceTermList();
        $serviceTerm = current($serviceTermList);
        echo "test service term \n";
        echo $serviceTerm[ServiceTerms::ID] . "\n";
        // TODO 期間判定
        if (empty($serviceTerm)) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_500,
                'Service Term Not Exist.'
            );
        }

        // 最新のIDでは無い場合
        if ($serviceTerm[ServiceTerms::ID] !== $serviceTermId) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_404,
                'Don\'t Match Service Term ID of Current Version.'
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
     * get record list & sort by version.
     *
     * @return array
     */
    private function getServiceTermList(): array
    {
        $cache = MasterCacheLibrary::getServiceTermsCache();

        // キャッシュチェック
        if (is_null($cache)) {
            $collection = $this->serviceTermsRepository->getRecords();
            if (empty($collection)) {
                // 空配列もキャッシュとして設定する
                MasterCacheLibrary::setServiceTermsCache([]);
                return [];
            }
            $records = ArrayLibrary::toArray($collection->toArray());

            MasterCacheLibrary::setServiceTermsCache($records);
        } else {
            $records = $cache;
        }
        return ServiceTerms::sortByVersion($records, SORT_DESC);
    }
}
