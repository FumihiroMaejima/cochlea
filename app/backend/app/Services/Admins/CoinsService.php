<?php

declare(strict_types=1);

namespace App\Services\Admins;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Http\Resources\Admins\CoinsResource;
use App\Repositories\Masters\Coins\CoinsRepositoryInterface;
use App\Exports\Masters\Coins\CoinsExport;
use App\Exports\Masters\Coins\CoinsBulkInsertTemplateExport;
use App\Imports\Masters\Coins\CoinsImport;
use App\Library\Array\ArrayLibrary;
use App\Library\Cache\CacheLibrary;
use App\Library\Time\TimeLibrary;
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
     * @param  \App\Repositories\Masters\Coins\CoinsRepositoryInterface $coinsRepository
     * @return void
     */
    public function __construct(CoinsRepositoryInterface $coinsRepository)
    {
        $this->coinsRepository = $coinsRepository;
    }

    /**
     * get coins data
     *
     * @return JsonResponse
     */
    public function getCoins(): JsonResponse
    {
        $cache = CacheLibrary::getByKey(self::CACHE_KEY_ADMIN_COIN_COLLECTION_LIST);

        // キャッシュチェック
        if (is_null($cache)) {
            $collection = $this->coinsRepository->getRecords();
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
     * download coin data service
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadCSV()
    {
        $data = $this->coinsRepository->getRecords();

        return Excel::download(new CoinsExport($data), 'coins_info_' . TimeLibrary::getCurrentDateTime(TimeLibrary::DATE_TIME_FORMAT_YMDHIS) . '.csv');
    }

    /**
     * download coin template data service
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadTemplate()
    {
        return Excel::download(
            new CoinsBulkInsertTemplateExport(collect(Config::get('myappFile.service.admins.coins.template'))),
            'master_coins_template_' . TimeLibrary::getCurrentDateTime(TimeLibrary::DATE_TIME_FORMAT_YMDHIS) . '.xlsx'
        );
    }


    /**
     * imort coins by template data service
     *
     * @param UploadedFile $file
     * @return JsonResponse
     */
    public function importTemplate(UploadedFile $file)
    {
        // ファイル名チェック
        if (!preg_match('/^master_coins_template_\d{14}\.xlsx/u', $file->getClientOriginalName())) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_422,
                'no include title.'
            );
        }

        DB::beginTransaction();
        try {
            // Excel::import(new EnemiesImport, $file, null, \Maatwebsite\Excel\Excel::XLSX);
            // Excel::import(new EnemiesImport($file), $file, null, \Maatwebsite\Excel\Excel::XLSX);
            $fileData = Excel::toArray(new CoinsImport($file), $file, null, \Maatwebsite\Excel\Excel::XLSX);

            // $resource = app()->make(GameEnemiesCreateResource::class, ['resource' => $fileData[0]])->toArray($request);
            $resource = CoinsResource::toArrayForBulkInsert(current($fileData));

            $insertCount = $this->coinsRepository->create($resource);

            DB::commit();

            // キャッシュの削除
            CacheLibrary::deleteCache(self::CACHE_KEY_ADMIN_COIN_COLLECTION_LIST, true);

            // レスポンスの制御
            $message = $insertCount ? 'success' : 'Bad Request';
            $status = $insertCount ? 201 : 401;

            return response()->json(['message' => $message, 'status' => $status], $status);
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'message: ' . json_encode($e->getMessage()));
            DB::rollback();
            abort(500);
        }
    }

    /**
     * create coin data service
     *
     * @param string $name name
     * @param string $detail detail
     * @param int $price price
     * @param int $cost cost
     * @param string $startAt start datetime
     * @param string $endAt end datetime
     * @param string|null $image image
     * @return \Illuminate\Http\JsonResponse
     */
    public function createCoin(string $name, string $detail, int $price, int $cost, string $startAt, string $endAt, string|null $image): JsonResponse
    {
        $resource = CoinsResource::toArrayForCreate($name, $detail, $price, $cost, $startAt, $endAt, $image);

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
     * @param int $id
     * @param string $name name
     * @param string $detail detail
     * @param int $price price
     * @param int $cost cost
     * @param string $startAt start datetime
     * @param string $endAt end datetime
     * @param string|null $image image
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCoin(int $id, string $name, string $detail, int $price, int $cost, string $startAt, string $endAt, string|null $image): JsonResponse
    {
        $resource = CoinsResource::toArrayForUpdate($name, $detail, $price, $cost, $startAt, $endAt, $image);

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
     * @param array<int, int> $coinIds
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteCoin(array $coinIds): JsonResponse
    {
        DB::beginTransaction();
        try {
            $resource = CoinsResource::toArrayForDelete();

            // ロックをかける為transaction内で実行
            $coins = $this->getCoinsByIds($coinIds);

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
                StatusCodeMessages::STATUS_500,
                'not exist coin.'
            );
        }

        // 複数チェックはrepository側で実施済み
        return ArrayLibrary::toArray(ArrayLibrary::getFirst($coins->toArray()));
    }

    /**
     * get coins by coin ids.
     *
     * @param array $coinIds coin id
     * @return array
     */
    private function getCoinsByIds(array $coinIds): array
    {
        // 更新用途で使う為lockをかける
        $coins = $this->coinsRepository->getByIds($coinIds, true);

        if (empty($coins)) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_500,
                'not exist coins.'
            );
        }

        // 複数チェックはrepository側で実施済み
        return ArrayLibrary::toArray($coins->toArray());
    }
}
