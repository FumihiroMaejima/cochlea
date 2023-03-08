<?php

namespace App\Http\Resources\Admins;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use App\Library\Array\ArrayLibrary;
use App\Library\Banner\BannerLibrary;
use App\Library\Time\TimeLibrary;
use App\Models\Masters\Banners;

class BannersResource extends JsonResource
{
    public const RESOURCE_KEY_DATA = 'data';
    public const RESOURCE_KEY_TEXT = 'text';
    public const RESOURCE_KEY_VALUE = 'value';

    public const RESOURCE_KEY_UUID = 'uuid';
    public const RESOURCE_KEY_NAME = 'name';
    public const RESOURCE_KEY_DETAIL = 'detail';
    public const RESOURCE_KEY_LOCATION = 'location';
    public const RESOURCE_KEY_PC_HEIGHT = 'pc_height';
    public const RESOURCE_KEY_PC_WIDTH = 'pc_width';
    public const RESOURCE_KEY_SP_HEIGHT = 'sp_height';
    public const RESOURCE_KEY_SP_WIDTH = 'sp_width';
    public const RESOURCE_KEY_COST = 'cost';
    public const RESOURCE_KEY_START_AT = 'start_at';
    public const RESOURCE_KEY_END_AT = 'end_at';
    public const RESOURCE_KEY_URL = 'url';
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
    public static function toArrayForGetCollection(Collection $collection)
    {
        // レスポンス
        $response = [];

        foreach ($collection as $item) {
            // 画像URL設定
            $item->{self::RESOURCE_KEY_IMAGE} = BannerLibrary::getAdminBannerPath(ArrayLibrary::toArray($item));

            $item->{Banners::START_AT} = TimeLibrary::format($item->{Banners::START_AT});
            $item->{Banners::END_AT} = TimeLibrary::format($item->{Banners::END_AT});

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
     * Transform the resource into an array for create.
     *
     * @param string $uuid uuid
     * @param string $name name
     * @param string $detail detail
     * @param int $location location vlaue
     * @param int $pcHeight pc height
     * @param int $pcWidth pc width
     * @param int $spHeight sp height
     * @param int $spWidth sp width
     * @param string $startAt start datetime
     * @param string $endAt end datetime
     * @param string $url url
     * @return array
     */
    public static function toArrayForCreate(
        string $uuid,
        string $name,
        string $detail,
        int $location,
        int $pcHeight,
        int $pcWidth,
        int $spHeight,
        int $spWidth,
        string $startAt,
        string $endAt,
        string $url
    ): array {
        $dateTime = TimeLibrary::getCurrentDateTime();

        return [
            self::RESOURCE_KEY_UUID       => $uuid,
            self::RESOURCE_KEY_NAME       => $name,
            self::RESOURCE_KEY_DETAIL     => $detail,
            self::RESOURCE_KEY_LOCATION   => $location,
            self::RESOURCE_KEY_PC_HEIGHT  => $pcHeight,
            self::RESOURCE_KEY_PC_WIDTH   => $pcWidth,
            self::RESOURCE_KEY_SP_HEIGHT  => $spHeight,
            self::RESOURCE_KEY_SP_WIDTH   => $spWidth,
            self::RESOURCE_KEY_START_AT   => $startAt,
            self::RESOURCE_KEY_END_AT     => $endAt,
            self::RESOURCE_KEY_URL        => $url,
            self::RESOURCE_KEY_CREATED_AT => $dateTime,
            self::RESOURCE_KEY_UPDATED_AT => $dateTime,
        ];
    }

    /**
     * Transform the resource into an array for update.
     *
     * @param string $uuid uuid
     * @param string $name name
     * @param string $detail detail
     * @param int $location location vlaue
     * @param int $pcHeight pc height
     * @param int $pcWidth pc width
     * @param int $spHeight sp height
     * @param int $spWidth sp width
     * @param string $startAt start datetime
     * @param string $endAt end datetime
     * @param string $url url
     * @return array
     */
    public static function toArrayForUpdate(
        string $uuid,
        string $name,
        string $detail,
        int $location,
        int $pcHeight,
        int $pcWidth,
        int $spHeight,
        int $spWidth,
        string $startAt,
        string $endAt,
        string $url
    ): array {
        $dateTime = TimeLibrary::getCurrentDateTime();

        return [
            self::RESOURCE_KEY_UUID       => $uuid,
            self::RESOURCE_KEY_NAME       => $name,
            self::RESOURCE_KEY_DETAIL     => $detail,
            self::RESOURCE_KEY_LOCATION   => $location,
            self::RESOURCE_KEY_PC_HEIGHT  => $pcHeight,
            self::RESOURCE_KEY_PC_WIDTH   => $pcWidth,
            self::RESOURCE_KEY_SP_HEIGHT  => $spHeight,
            self::RESOURCE_KEY_SP_WIDTH   => $spWidth,
            self::RESOURCE_KEY_START_AT   => $startAt,
            self::RESOURCE_KEY_END_AT     => $endAt,
            self::RESOURCE_KEY_URL        => $url,
            self::RESOURCE_KEY_CREATED_AT => $dateTime,
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
                    Banners::UUID       => $item[0],
                    Banners::NAME       => $item[1],
                    Banners::DETAIL     => $item[2],
                    Banners::LOCATION   => $item[3],
                    Banners::PC_HEIGHT  => $item[4],
                    Banners::PC_WIDTH   => $item[5],
                    Banners::SP_HEIGHT  => $item[6],
                    Banners::SP_WIDTH   => $item[7],
                    Banners::START_AT   => $item[8],
                    Banners::END_AT     => $item[9],
                    Banners::URL        => $item[10],
                    Banners::CREATED_AT => $dateTime,
                    Banners::UPDATED_AT => $dateTime,
                ];
            }
        }

        return $response;
    }
}
