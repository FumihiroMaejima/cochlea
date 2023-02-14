<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Users\UserCoinHistories;

class UserCoinHistoryResource extends JsonResource
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
     * Transform the resource into an array for create.
     *
     * @param array $records レコードリスト
     * @param string $serviceId 決済サービスの決済ID
     * @return array
     */
    public static function toArrayForList(array $records): array
    {
        $response = [];

        foreach ($records as  $record) {
            $response[] = [
                UserCoinHistories::USER_ID => $record[UserCoinHistories::USER_ID],
                UserCoinHistories::TYPE => UserCoinHistories::USER_COINS_HISTORY_TYPE_VALUE_LIST[$record[UserCoinHistories::TYPE]],
                UserCoinHistories::GET_FREE_COINS => $record[UserCoinHistories::GET_FREE_COINS],
                UserCoinHistories::GET_PAID_COINS => $record[UserCoinHistories::GET_PAID_COINS],
                UserCoinHistories::GET_LIMITED_TIME_COINS => $record[UserCoinHistories::GET_LIMITED_TIME_COINS],
                UserCoinHistories::USED_FREE_COINS => $record[UserCoinHistories::USED_FREE_COINS],
                UserCoinHistories::USED_PAID_COINS => $record[UserCoinHistories::USED_PAID_COINS],
                UserCoinHistories::USED_LIMITED_TIME_COINS => $record[UserCoinHistories::USED_LIMITED_TIME_COINS],
                UserCoinHistories::EXPIRED_LIMITED_TIME_COINS => $record[UserCoinHistories::EXPIRED_LIMITED_TIME_COINS],
                UserCoinHistories::UPDATED_AT => $record[UserCoinHistories::UPDATED_AT],
            ];
        }

        return $response;
    }
}
