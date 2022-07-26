<?php

namespace App\Http\Resources\Logs;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use App\Http\Requests\Admins\Coins\CoinCreateRequest;
use App\Http\Requests\Admins\Coins\CoinUpdateRequest;
use App\Library\Time\TimeLibrary;
use App\Models\Logs\UserCoinPaymentLog;
use App\Models\Masters\Coins;
use App\Models\Users\UserCoins;
use App\Models\Users\UserCoinPaymentStatus;

class UserCoinPaymentLogResource extends JsonResource
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
     * @param int $userId
     * @param string $orderId
     * @param int $coinId
     * @param int $status
     * @return array
     */
    public static function toArrayForCreate(int $userId, string $orderId, int $coinId, int $status): array
    {
        $dateTime = TimeLibrary::getCurrentDateTime();

        return [
            UserCoinPaymentLog::USER_ID => $userId,
            UserCoinPaymentLog::ORDER_ID => $orderId,
            UserCoinPaymentLog::COIN_ID => $coinId,
            UserCoinPaymentLog::STATUS => $status,
            UserCoinPaymentLog::CRREATED_AT => $dateTime,
            UserCoinPaymentLog::UPDATED_AT => $dateTime,
            UserCoinPaymentLog::DELETED_AT => null,
        ];
    }

    /**
     * Transform the resource into an array for update.
     *
     * @param CoinUpdateRequest $request
     * @return array
     */
    public static function toArrayForUpdate(CoinUpdateRequest $request): array
    {
        $dateTime = TimeLibrary::getCurrentDateTime();

        return [
            UserCoinPaymentLog::USER_ID => $request->{UserCoinPaymentLog::USER_ID},
            UserCoinPaymentLog::ORDER_ID => $request->{UserCoinPaymentLog::ORDER_ID},
            UserCoinPaymentLog::COIN_ID => $request->{UserCoinPaymentLog::COIN_ID},
            UserCoinPaymentLog::STATUS => $request->{UserCoinPaymentLog::STATUS},
            UserCoinPaymentLog::CRREATED_AT => $dateTime,
            UserCoinPaymentLog::UPDATED_AT => $dateTime,
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
            UserCoinPaymentLog::UPDATED_AT => $dateTime,
            UserCoinPaymentLog::DELETED_AT => $dateTime,
        ];
    }
}
