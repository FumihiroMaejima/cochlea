<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use App\Library\Time\TimeLibrary;
use App\Models\Masters\HomeContents;
use App\Models\Masters\HomeContentsGroups;
use App\Models\Masters\BannerBlockContents;
use App\Models\Masters\BannerBlocks;

class HomeContentsResource extends JsonResource
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
            $item->{HomeContents::START_AT} = TimeLibrary::format($item->{HomeContents::START_AT});
            $item->{HomeContents::END_AT} = TimeLibrary::format($item->{HomeContents::END_AT});

            // if array
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
                self::RESOURCE_KEY_TEXT => $item->{HomeContents::CONTENTS_ID},
                self::RESOURCE_KEY_VALUE => $item->{HomeContents::ID},
            ];
            // 多次元配列の中の連想配列を格納
            $response[self::RESOURCE_KEY_DATA][] = $role;
        }

        return $response;
    }

    /**
     * Transform the resource into an array for get text => value list.
     *
     * @param array $bannerBlocks
     * @param array $bannerBlockContentsList
     * @return array
     */
    public static function toArrayForGetBannerBlockResponse(array $bannerBlocks, array $bannerBlockContentsList)
    {
        // レスポンス
        $response = [];

        $contentsGroupByBlockId = [];
        foreach ($bannerBlockContentsList as $bannerBlockContents) {
            $contentsGroupByBlockId[$bannerBlockContents[BannerBlockContents::BANNER_BLOCK_ID]][] = $bannerBlockContents;
        }

        // $this->resourceはCollection
        // 各itemは1レコードずつのデータを持つRolesResourceクラス
        foreach ($bannerBlocks as $bannerBlock) {
            if (isset($bannerBlockContentsList[$bannerBlock[BannerBlocks::ID]])) {
                $response[$bannerBlock[BannerBlocks::ID]] = [
                    'name' => $bannerBlock[BannerBlocks::NAME],
                    'list' => $bannerBlockContentsList[$bannerBlock[BannerBlocks::ID]],
                ];
            }
        }

        return $response;
    }
}
