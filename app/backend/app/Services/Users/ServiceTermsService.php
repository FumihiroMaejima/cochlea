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
use App\Library\Array\ArrayLibrary;
use App\Library\Cache\MasterCacheLibrary;
use App\Models\Masters\ServiceTerms;
use Exception;

class ServiceTermsService
{
    protected ServiceTermsRepositoryInterface $serviceTermsRepository;

    /**
     * create service instance
     *
     * @param ServiceTermsRepositoryInterface $serviceTermsRepository
     * @return void
     */
    public function __construct(
        ServiceTermsRepositoryInterface $serviceTermsRepository
    ) {
        $this->serviceTermsRepository = $serviceTermsRepository;
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
}
