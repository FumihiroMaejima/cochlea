<?php

namespace App\Http\Resources\Admins;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use App\Http\Requests\Admin\Coins\CoinCreateRequest;
use App\Http\Requests\Admin\Coins\CoinUpdateRequest;
use App\Library\Time\TimeLibrary;
use App\Models\Masters\Coins;

class CoinsResource extends JsonResource
{
    public const RESOURCE_KEY_DATA = 'data';
    public const RESOURCE_KEY_TEXT = 'text';
    public const RESOURCE_KEY_VALUE = 'value';

    public const RESOURCE_KEY_NAME = 'name';
    public const RESOURCE_KEY_DETAIL = 'detail';
    public const RESOURCE_KEY_PRICE = 'price';
    public const RESOURCE_KEY_COST = 'cost';
    public const RESOURCE_KEY_START_AT = 'start_at';
    public const RESOURCE_KEY_END_AT = 'end_at';
    public const RESOURCE_KEY_IMAGE = 'image';
    public const RESOURCE_KEY_CREATED_AT = 'created_at';
    public const RESOURCE_KEY_UPDATED_AT = 'updated_at';
    public const RESOURCE_KEY_DELETED_AT = 'deleted_at';

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // $this->resourceはcollection
        // $this->resource->itemsは検索結果の各レコードをstdClassオブジェクトとして格納した配列
        return [
            self::RESOURCE_KEY_DATA => $this->resource->toArray($request)
        ];
    }

    /**
     * Transform the resource into an array for get roles collection.
     *
     * @param Collection $collection
     * @return array
     */
    public static function toArrayForGetCoinsCollection(Collection $collection)
    {
        // レスポンス
        $response = [];

        foreach ($collection as $item) {
            $item->{Coins::START_AT} = TimeLibrary::format($item->{Coins::START_AT});
            $item->{Coins::END_AT} = TimeLibrary::format($item->{Coins::END_AT});

            // if array
            // $item[Coins::START_AT] = TimeLibrary::format($item[Coins::START_AT]);
            // $item[Coins::END_AT] = TimeLibrary::format($item[Coins::END_AT]);
            $response[self::RESOURCE_KEY_DATA][] = $item;
        }

        return $response;
    }

    /**
     * Transform the resource into an array for get text => value list.
     *
     * @param Collection $collection
     * @return array
     */
    public static function toArrayForGetTextAndValueList(Collection $collection)
    {
        // レスポンス
        $response = [];

        // $this->resourceはCollection
        // 各itemは1レコードずつのデータを持つRolesResourceクラス
        foreach ($collection as $item) {
            // 各itemのresourceはstdClassオブジェクトの１レコード分のデータ
            $role = [
                self::RESOURCE_KEY_TEXT => $item->{Coins::NAME},
                self::RESOURCE_KEY_VALUE => $item->{Coins::ID},
            ];
            // 多次元配列の中の連想配列を格納
            $response[self::RESOURCE_KEY_DATA][] = $role;
        }

        return $response;
    }

    /**
     * Transform the resource into an array for create.
     *
     * @param string $name name
     * @param string $detail detail
     * @param int $price price
     * @param int $cost cost
     * @param string $startAt start datetime
     * @param string $endAt end datetime
     * @param string|null $image image
     * @return array
     */
    public static function toArrayForCreate(string $name, string $detail, int $price, int $cost, string $startAt, string $endAt, string|null $image): array
    {
        $dateTime = TimeLibrary::getCurrentDateTime();

        return [
            self::RESOURCE_KEY_NAME       => $name,
            self::RESOURCE_KEY_DETAIL     => $detail,
            self::RESOURCE_KEY_PRICE      => $price,
            self::RESOURCE_KEY_COST       => $cost,
            self::RESOURCE_KEY_START_AT   => $startAt,
            self::RESOURCE_KEY_END_AT     => $endAt,
            self::RESOURCE_KEY_IMAGE      => $image ?? '',
            self::RESOURCE_KEY_CREATED_AT => $dateTime,
            self::RESOURCE_KEY_UPDATED_AT => $dateTime,
        ];
    }

    /**
     * Transform the resource into an array for update.
     *
     * @param string $name name
     * @param string $detail detail
     * @param int $price price
     * @param int $cost cost
     * @param string $startAt start datetime
     * @param string $endAt end datetime
     * @param string|null $image image
     * @return array
     */
    public static function toArrayForUpdate(string $name, string $detail, int $price, int $cost, string $startAt, string $endAt, string|null $image): array
    {
        $dateTime = TimeLibrary::getCurrentDateTime();

        return [
            self::RESOURCE_KEY_NAME       => $name,
            self::RESOURCE_KEY_DETAIL     => $detail,
            self::RESOURCE_KEY_PRICE      => $price,
            self::RESOURCE_KEY_COST       => $cost,
            self::RESOURCE_KEY_START_AT   => $startAt,
            self::RESOURCE_KEY_END_AT     => $endAt,
            self::RESOURCE_KEY_IMAGE      => $image ?? '',
            self::RESOURCE_KEY_UPDATED_AT => $dateTime
        ];
    }

    /**
     * Transform the resource into an array for delete.
     *
     * @return array
     */
    public static function toArrayForDelete(): array
    {
        $dateTime = TimeLibrary::getCurrentDateTime();

        return [
            self::RESOURCE_KEY_UPDATED_AT => $dateTime,
            self::RESOURCE_KEY_DELETED_AT => $dateTime
        ];
    }

    /**
     * Transform the resource into an array for bulk insert.
     *
     * @param array $resouce
     * @return array
     */
    public static function toArrayForBulkInsert(array $resouce)
    {
        $dateTime = TimeLibrary::getCurrentDateTime();

        $response = [];

        foreach ($resouce as $key => $item) {
            // 先頭はファイルのヘッダーに当たる為除外
            if ($key !== 0) {
                $response[] = [
                    Coins::NAME       => $item[0],
                    Coins::DETAIL     => $item[1],
                    Coins::PRICE      => $item[2],
                    Coins::COST       => $item[3],
                    Coins::START_AT   => $item[4],
                    Coins::END_AT     => $item[5],
                    Coins::IMAGE      => $item[6] ?? '',
                    Coins::CREATED_AT => $dateTime,
                    Coins::UPDATED_AT => $dateTime,
                ];
            }
        }

        return $response;
    }
}
