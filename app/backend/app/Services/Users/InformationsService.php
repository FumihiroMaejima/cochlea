<?php

namespace App\Services\Users;

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
use App\Http\Resources\Users\InformationsResource;
use App\Repositories\Admins\Informations\InformationsRepositoryInterface;
use App\Library\Array\ArrayLibrary;
use App\Library\Cache\CacheLibrary;
use Exception;

class InformationsService
{
    // cache keys
    private const CACHE_KEY_USER_INFORMATION_LIST = 'cache_user_information_list';

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
     * @throws Exception
     */
    public function getInformations(): JsonResponse
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
            $resourceCollection = $cache;
        }

        return response()->json($resourceCollection, 200);
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
