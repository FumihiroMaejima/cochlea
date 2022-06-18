<?php

namespace App\Http\Resources\Admins;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use App\Http\Requests\Admins\RoleUpdateRequest;
use App\Http\Requests\Admins\RoleCreateRequest;
use App\Library\TimeLibrary;

class RolesResource extends JsonResource
{
    public const RESOURCE_KEY_DATA = 'data';
    public const RESOURCE_KEY_TEXT = 'text';
    public const RESOURCE_KEY_VALUE = 'value';

    public const RESOURCE_KEY_NAME = 'name';
    public const RESOURCE_KEY_CODE = 'code';
    public const RESOURCE_KEY_DETAIL = 'detail';
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
                self::RESOURCE_KEY_TEXT => $item->name,
                self::RESOURCE_KEY_VALUE => $item->id,
            ];
            // 多次元配列の中の連想配列を格納
            $response[self::RESOURCE_KEY_DATA][] = $role;
        }

        return $response;
    }

    /**
     * Transform the resource into an array for create.
     *
     * @param RoleCreateRequest $request
     * @return array
     */
    public static function toArrayForCreate(RoleCreateRequest $request): array
    {
        /* $carbon = new Carbon();
        $test = $carbon->now()->format('Y-m-d H:i:s'); */
        $dateTime = TimeLibrary::getCurrentDateTime();

        return [
            self::RESOURCE_KEY_NAME        => $request->name,
            self::RESOURCE_KEY_CODE        => $request->code,
            self::RESOURCE_KEY_DETAIL      => $request->detail,
            self::RESOURCE_KEY_CREATED_AT  => $dateTime,
            self::RESOURCE_KEY_UPDATED_AT  => $dateTime
        ];
    }

    /**
     * Transform the resource into an array for update.
     *
     * @param $request
     * @return array
     */
    public static function toArrayForUpdate(RoleUpdateRequest $request): array
    {
        $dateTime = TimeLibrary::getCurrentDateTime();

        return [
            self::RESOURCE_KEY_NAME        => $request->name,
            self::RESOURCE_KEY_CODE        => $request->code,
            self::RESOURCE_KEY_DETAIL      => $request->detail,
            self::RESOURCE_KEY_UPDATED_AT  => $dateTime
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
}
