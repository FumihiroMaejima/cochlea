<?php

declare(strict_types=1);

namespace App\Http\Resources\Admins;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use App\Library\Time\TimeLibrary;
use App\Models\Masters\BannerBlockContents;

class BannerBlockContentsResource extends JsonResource
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
            $item->{BannerBlockContents::START_AT} = TimeLibrary::format($item->{BannerBlockContents::START_AT});
            $item->{BannerBlockContents::END_AT} = TimeLibrary::format($item->{BannerBlockContents::END_AT});

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
                self::RESOURCE_KEY_TEXT => $item->{BannerBlockContents::BANNER_ID},
                self::RESOURCE_KEY_VALUE => $item->{BannerBlockContents::ID},
            ];
            // 多次元配列の中の連想配列を格納
            $response[self::RESOURCE_KEY_DATA][] = $role;
        }

        return $response;
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
                    BannerBlockContents::BANNER_BLOCK_ID => $item[0],
                    BannerBlockContents::BANNER_ID       => $item[1],
                    BannerBlockContents::TYPE            => $item[2],
                    BannerBlockContents::ORDER           => $item[3],
                    BannerBlockContents::START_AT        => $item[4],
                    BannerBlockContents::END_AT          => $item[5],
                    BannerBlockContents::CREATED_AT      => $dateTime,
                    BannerBlockContents::UPDATED_AT      => $dateTime,
                ];
            }
        }

        return $response;
    }
}
