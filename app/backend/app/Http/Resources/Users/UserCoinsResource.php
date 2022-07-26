<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use App\Library\Time\TimeLibrary;
use App\Models\Masters\Coins;
use App\Models\Users\UserCoins;

class UserCoinsResource extends JsonResource
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
     * @param int $userId ユーザーID
     * @param int $freeCoins 無料コイン数
     * @param int $paidCoins 無料コイン数
     * @param int $limitedTimeCoins 期限付きコイン数
     * @return array
     */
    public static function toArrayForCreate(int $userId, int $freeCoins, int $paidCoins, int $limitedTimeCoins): array
    {
        $dateTime = TimeLibrary::getCurrentDateTime();

        return [
            UserCoins::USER_ID => $userId,
            UserCoins::FREE_COINS => $freeCoins,
            UserCoins::PAID_COINS => $paidCoins,
            UserCoins::LIMITED_TIME_COINS => $limitedTimeCoins,
            UserCoins::CRREATED_AT => $dateTime,
            UserCoins::UPDATED_AT => $dateTime,
            UserCoins::DELETED_AT => null,
        ];
    }

    /**
     * Transform the resource into an array for update.
     *
     * @param int $userId ユーザーID
     * @param int $freeCoins 無料コイン数
     * @param int $paidCoins 無料コイン数
     * @param int $limitedTimeCoins 期限付きコイン数
     * @return array
     */
    public static function toArrayForUpdate(int $userId, int $freeCoins, int $paidCoins, int $limitedTimeCoins): array
    {
        $dateTime = TimeLibrary::getCurrentDateTime();

        return [
            UserCoins::USER_ID => $userId,
            UserCoins::FREE_COINS => $freeCoins,
            UserCoins::PAID_COINS => $paidCoins,
            UserCoins::LIMITED_TIME_COINS => $limitedTimeCoins,
            UserCoins::CRREATED_AT => $dateTime,
            UserCoins::UPDATED_AT => $dateTime,
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
            UserCoins::UPDATED_AT => $dateTime,
            UserCoins::DELETED_AT => $dateTime,
        ];
    }
}
