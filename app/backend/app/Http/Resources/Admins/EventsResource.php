<?php

declare(strict_types=1);

namespace App\Http\Resources\Admins;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use App\Library\Time\TimeLibrary;
use App\Models\Masters\Events;

class EventsResource extends JsonResource
{
    public const RESOURCE_KEY_DATA = 'data';
    public const RESOURCE_KEY_TEXT = 'text';
    public const RESOURCE_KEY_VALUE = 'value';

    public const RESOURCE_KEY_NAME = 'name';
    public const RESOURCE_KEY_TYPE = 'type';
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
    public static function toArrayForGetCollectionList(Collection $collection)
    {
        // レスポンス
        $response = [];

        foreach ($collection as $item) {
            $item->{Events::START_AT} = TimeLibrary::format($item->{Events::START_AT});
            $item->{Events::END_AT} = TimeLibrary::format($item->{Events::END_AT});

            // if array
            // $item[Events::START_AT] = TimeLibrary::format($item[Events::START_AT]);
            // $item[Events::END_AT] = TimeLibrary::format($item[Events::END_AT]);
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
                self::RESOURCE_KEY_TEXT => $item->{Events::NAME},
                self::RESOURCE_KEY_VALUE => $item->{Events::ID},
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
     * @param int $type type
     * @param string $detail detail
     * @param string $startAt start datetime
     * @param string $endAt end datetime
     * @return array
     */
    public static function toArrayForCreate(string $name, int $type, string $detail, string $startAt, string $endAt): array
    {
        $dateTime = TimeLibrary::getCurrentDateTime();

        return [
            self::RESOURCE_KEY_NAME       => $name,
            self::RESOURCE_KEY_TYPE       => $type,
            self::RESOURCE_KEY_DETAIL     => $detail,
            self::RESOURCE_KEY_START_AT   => $startAt,
            self::RESOURCE_KEY_END_AT     => $endAt,
            self::RESOURCE_KEY_CREATED_AT => $dateTime,
            self::RESOURCE_KEY_UPDATED_AT => $dateTime,
        ];
    }

    /**
     * Transform the resource into an array for update.
     *
     * @param string $name name
     * @param int $type type
     * @param string $detail detail
     * @param string $startAt start datetime
     * @param string $endAt end datetime
     * @return array
     */
    public static function toArrayForUpdate(string $name, int $type, string $detail, string $startAt, string $endAt): array
    {
        $dateTime = TimeLibrary::getCurrentDateTime();

        return [
            self::RESOURCE_KEY_NAME       => $name,
            self::RESOURCE_KEY_TYPE       => $type,
            self::RESOURCE_KEY_DETAIL     => $detail,
            self::RESOURCE_KEY_START_AT   => $startAt,
            self::RESOURCE_KEY_END_AT     => $endAt,
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
                    Events::NAME       => $item[0],
                    Events::TYPE       => $item[1],
                    Events::DETAIL     => $item[2],
                    Events::START_AT   => $item[3],
                    Events::END_AT     => $item[4],
                    Events::CREATED_AT => $dateTime,
                    Events::UPDATED_AT => $dateTime,
                ];
            }
        }

        return $response;
    }
}
