<?php

namespace App\Services\Admins;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Exceptions\MyApplicationHttpException;
use App\Exceptions\ExceptionStatusCodeMessages;
use App\Http\Requests\Admins\Coins\CoinCreateRequest;
use App\Http\Requests\Admins\Coins\CoinDeleteRequest;
use App\Http\Requests\Admins\Coins\CoinUpdateRequest;
use App\Http\Resources\Admins\CoinsResource;
use App\Repositories\Admins\Coins\CoinsRepositoryInterface;
use App\Repositories\Admins\Roles\RolesRepositoryInterface;
use App\Exports\Admins\RolesExport;
use App\Library\Array\ArrayLibrary;
use App\Library\Cache\CacheLibrary;
use App\Models\Masters\Coins;
use Exception;

class CoinsService
{
    // cache keys
    private const CACHE_KEY_ADMIN_COIN_COLLECTION_LIST = 'admin_coin_collection_list';

    protected CoinsRepositoryInterface $coinsRepository;

    /**
     * create CoinsService instance
     *
     * @param  \App\Repositories\Admins\Coins\CoinsRepositoryInterface $coinsRepository
     * @return void
     */
    public function __construct(CoinsRepositoryInterface $coinsRepository)
    {
        $this->coinsRepository = $coinsRepository;
    }

    /**
     * get coins data
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    public function getCoins(Request $request): JsonResponse
    {
        $cache = CacheLibrary::getByKey(self::CACHE_KEY_ADMIN_COIN_COLLECTION_LIST);

        // キャッシュチェック
        if (is_null($cache)) {
            $collection = $this->coinsRepository->getCoins();
            $resourceCollection = CoinsResource::toArrayForGetCoinsCollection($collection);

            if (!empty($resourceCollection)) {
                CacheLibrary::setCache(self::CACHE_KEY_ADMIN_COIN_COLLECTION_LIST, $resourceCollection);
            }
        } else {
            $resourceCollection = $cache;
        }

        return response()->json($resourceCollection, 200);
    }

    /**
     * download role data service
     *
     * @param  \Illuminate\Http\Request;  $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadCSV(Request $request)
    {
        $data = $this->coinsRepository->getCoins();

        return Excel::download(new RolesExport($data), 'coins_info_' . Carbon::now()->format('YmdHis') . '.csv');
    }

    /**
     * update coin data service
     *
     * @param  CoinCreateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function createCoin(CoinCreateRequest $request): JsonResponse
    {
        $resource = CoinsResource::toArrayForCreate($request);

        DB::beginTransaction();
        try {
            $insertCount = $this->coinsRepository->create($resource);

            DB::commit();

            // キャッシュの削除
            CacheLibrary::deleteCache(self::CACHE_KEY_ADMIN_COIN_COLLECTION_LIST, true);

            // 作成されている場合は304
            $message = ($insertCount > 0) ? 'success' : 'Bad Request';
            $status = ($insertCount > 0) ? 201 : 401;

            return response()->json(['message' => $message, 'status' => $status], $status);
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'message: ' . json_encode($e->getMessage()));
            DB::rollback();
            abort(500);
        }
    }

    /**
     * update coin data service
     *
     * @param  CoinUpdateRequest $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCoin(CoinUpdateRequest $request, int $id): JsonResponse
    {
        $resource = CoinsResource::toArrayForUpdate($request);

        DB::beginTransaction();
        try {
            // ロックをかける為transaction内で実行
            $coin = $this->getCoinById($id);
            $updatedRowCount = $this->coinsRepository->update($coin[Coins::ID], $resource);

            DB::commit();

            // キャッシュの削除
            CacheLibrary::deleteCache(self::CACHE_KEY_ADMIN_COIN_COLLECTION_LIST, true);

            // 更新されていない場合は304
            $message = ($updatedRowCount > 0) ? 'success' : 'not modified';
            $status = ($updatedRowCount > 0) ? 200 : 304;

            return response()->json(['message' => $message, 'status' => $status], $status);
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'message: ' . json_encode($e->getMessage()));
            DB::rollback();
            abort(500);
        }
    }

    /**
     * delete coin data service
     *
     * @param  CoinDeleteRequest $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteCoin(CoinDeleteRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $coinIds = $request->coins;

            $resource = CoinsResource::toArrayForDelete();

            $deleteRowCount = $this->coinsRepository->delete($coinIds, $resource);

            DB::commit();

            // キャッシュの削除
            CacheLibrary::deleteCache(self::CACHE_KEY_ADMIN_COIN_COLLECTION_LIST, true);

            // 更新されていない場合は304
            $message = ($deleteRowCount > 0) ? 'success' : 'not deleted';
            $status = ($deleteRowCount > 0) ? 200 : 401;

            return response()->json(['message' => $message, 'status' => $status], $status);
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'message: ' . json_encode($e->getMessage()));
            DB::rollback();
            abort(500);
        }
    }

    /**
     * get coin by coin id.
     *
     * @param int $coinId coin id
     * @return array
     */
    private function getCoinById(int $coinId): array
    {
        // 更新用途で使う為lockをかける
        $coins = $this->coinsRepository->getById($coinId, true);

        if (empty($coins)) {
            throw new MyApplicationHttpException(
                ExceptionStatusCodeMessages::STATUS_CODE_500,
                'not exist coin.'
            );
        }

        // 複数チェックはrepository側で実施済み
        return ArrayLibrary::toArray(ArrayLibrary::getFirst($coins->toArray()));
    }
}
