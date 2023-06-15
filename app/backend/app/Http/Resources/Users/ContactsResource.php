<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use App\Library\Time\TimeLibrary;
use App\Models\Masters\Contacts;

class ContactsResource extends JsonResource
{
    public const RESOURCE_KEY_DATA = 'data';
    public const RESOURCE_KEY_TEXT = 'text';
    public const RESOURCE_KEY_VALUE = 'value';

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
                self::RESOURCE_KEY_TEXT => $item->{Contacts::NAME},
                self::RESOURCE_KEY_VALUE => $item->{Contacts::ID},
            ];
            // 多次元配列の中の連想配列を格納
            $response[self::RESOURCE_KEY_DATA][] = $role;
        }

        return $response;
    }

    /**
     * Transform the resource into an array for create.
     *
     * @param string $email email
     * @param int $userId user id
     * @param string $name name
     * @param int $type type
     * @param string $detail detail
     * @param string $failureDetail failure detail
     * @param string $failureAt failure datetime
     * @return array
     */
    public static function toArrayForCreate(
        string $email,
        int $userId,
        string $name,
        int $type,
        string $detail,
        string $failureDetail,
        string $failureAt
    ): array {
        $dateTime = TimeLibrary::getCurrentDateTime();

        return [
            Contacts::EMAIL          => $email,
            Contacts::USER_ID        => $userId,
            Contacts::NAME           => $name,
            Contacts::TYPE           => $type,
            Contacts::DETAIL         => $detail,
            Contacts::FAILURE_DETAIL => $failureDetail,
            Contacts::FAILURE_AT     => $failureAt,
            Contacts::CREATED_AT     => $dateTime,
            Contacts::UPDATED_AT     => $dateTime
        ];
    }

    /**
     * Transform the resource into an array for update.
     *
     * @param string $email email
     * @param int $userId user id
     * @param string $name name
     * @param int $type type
     * @param string $detail detail
     * @param string $failureDetail failure detail
     * @param string $failureAt failure datetime
     * @return array
     */
    public static function toArrayForUpdate(
        string $email,
        int $userId,
        string $name,
        int $type,
        string $detail,
        string $failureDetail,
        string $failureAt
    ): array {
        return [
            Contacts::EMAIL          => $email,
            Contacts::USER_ID        => $userId,
            Contacts::NAME           => $name,
            Contacts::TYPE           => $type,
            Contacts::DETAIL         => $detail,
            Contacts::FAILURE_DETAIL => $failureDetail,
            Contacts::FAILURE_AT     => $failureAt,
            Contacts::UPDATED_AT     => TimeLibrary::getCurrentDateTime()
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
            Contacts::UPDATED_AT => $dateTime,
            Contacts::DELETED_AT => $dateTime
        ];
    }
}
