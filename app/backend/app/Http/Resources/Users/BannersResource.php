<?php

declare(strict_types=1);

namespace App\Http\Resources\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use App\Library\Array\ArrayLibrary;
use App\Library\Banner\BannerLibrary;
use App\Library\String\UuidLibrary;
use App\Library\Time\TimeLibrary;
use App\Models\Masters\Banners;

class BannersResource extends JsonResource
{
    public const RESOURCE_KEY_DATA = 'data';
    public const RESOURCE_KEY_TEXT = 'text';
    public const RESOURCE_KEY_VALUE = 'value';

    public const RESOURCE_KEY_UUID = 'uuid';
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
            $response[self::RESOURCE_KEY_DATA][] = [
                Banners::ID              => $item->{Banners::ID},
                Banners::UUID            => $item->{Banners::UUID},
                Banners::NAME            => $item->{Banners::NAME},
                Banners::DETAIL          => $item->{Banners::DETAIL},
                Banners::LOCATION        => $item->{Banners::LOCATION},
                Banners::PC_HEIGHT       => $item->{Banners::PC_HEIGHT},
                Banners::PC_WIDTH        => $item->{Banners::PC_WIDTH},
                Banners::SP_HEIGHT       => $item->{Banners::SP_HEIGHT},
                Banners::SP_WIDTH        => $item->{Banners::SP_WIDTH},
                Banners::START_AT        => TimeLibrary::format($item->{Banners::START_AT}),
                Banners::END_AT          => TimeLibrary::format($item->{Banners::END_AT}),
                Banners::URL             => $item->{Banners::URL},
                self::RESOURCE_KEY_IMAGE => BannerLibrary::getUserServiceBannerPath(ArrayLibrary::toArray($item)),   // 画像URL設定
                Banners::CREATED_AT      => $item->{Banners::CREATED_AT},
                Banners::UPDATED_AT      => $item->{Banners::UPDATED_AT},
            ];
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
                self::RESOURCE_KEY_TEXT => $item->{Banners::NAME},
                self::RESOURCE_KEY_UUID => $item->{Banners::UUID},
                self::RESOURCE_KEY_VALUE => $item->{Banners::ID},
            ];
            // 多次元配列の中の連想配列を格納
            $response[self::RESOURCE_KEY_DATA][] = $role;
        }

        return $response;
    }

    /**
     * Transform the resource into an array for get roles collection.
     *
     * @param array $records
     * @return array
     */
    public static function toArrayForGetCollectionListByArray(array $records)
    {
        // レスポンス
        $response = [];

        foreach ($records as $item) {
            $response[self::RESOURCE_KEY_DATA][] = [
                Banners::ID              => $item[Banners::ID],
                Banners::UUID            => $item[Banners::UUID],
                Banners::NAME            => $item[Banners::NAME],
                Banners::DETAIL          => $item[Banners::DETAIL],
                Banners::LOCATION        => $item[Banners::LOCATION],
                Banners::PC_HEIGHT       => $item[Banners::PC_HEIGHT],
                Banners::PC_WIDTH        => $item[Banners::PC_WIDTH],
                Banners::SP_HEIGHT       => $item[Banners::SP_HEIGHT],
                Banners::SP_WIDTH        => $item[Banners::SP_WIDTH],
                Banners::START_AT        => TimeLibrary::format($item[Banners::START_AT]),
                Banners::END_AT          => TimeLibrary::format($item[Banners::END_AT]),
                Banners::URL             => $item[Banners::URL],
                self::RESOURCE_KEY_IMAGE => BannerLibrary::getUserServiceBannerPath(ArrayLibrary::toArray($item)),   // 画像URL設定
                Banners::CREATED_AT      => $item[Banners::CREATED_AT],
                Banners::UPDATED_AT      => $item[Banners::UPDATED_AT],
            ];
        }

        return $response;
    }
}
