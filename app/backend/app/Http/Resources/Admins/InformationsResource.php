<?php

namespace App\Http\Resources\Admins;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use App\Http\Requests\Admin\Coins\CoinCreateRequest;
use App\Http\Requests\Admin\Coins\CoinUpdateRequest;
use App\Http\Requests\Admin\Informations\InformationCreateRequest;
use App\Http\Requests\Admin\Informations\InformationDeleteRequest;
use App\Http\Requests\Admin\Informations\InformationImportRequest;
use App\Http\Requests\Admin\Informations\InformationUpdateRequest;
use App\Library\Time\TimeLibrary;
use App\Models\Masters\Coins;
use App\Models\Masters\Informations;

class InformationsResource extends JsonResource
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
    public static function toArrayForGetInformationsCollection(Collection $collection)
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
                self::RESOURCE_KEY_TEXT => $item->{Informations::NAME},
                self::RESOURCE_KEY_VALUE => $item->{Informations::ID},
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
     * @param InformationUpdateRequest $request
     * @return array
     */
    public static function toArrayForUpdate(InformationUpdateRequest $request): array
    {
        $dateTime = TimeLibrary::getCurrentDateTime();

        return [
            self::RESOURCE_KEY_NAME       => $request->{Informations::NAME},
            self::RESOURCE_KEY_TYPE       => $request->{Informations::TYPE},
            self::RESOURCE_KEY_DETAIL     => $request->{Informations::DETAIL},
            self::RESOURCE_KEY_START_AT   => $request->{Informations::START_AT},
            self::RESOURCE_KEY_END_AT     => $request->{Informations::END_AT},
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
                    Informations::NAME       => $item[0],
                    Informations::TYPE       => $item[1],
                    Informations::DETAIL     => $item[2],
                    Informations::START_AT   => $item[3],
                    Informations::END_AT     => $item[4],
                    Informations::CREATED_AT => $dateTime,
                    Informations::UPDATED_AT => $dateTime,
                ];
            }
        }

        return $response;
    }
}
