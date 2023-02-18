<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use App\Http\Requests\Admin\Coins\CoinCreateRequest;
use App\Http\Requests\Admin\Coins\CoinUpdateRequest;
use App\Library\Time\TimeLibrary;
use App\Models\Masters\Coins;
use App\Models\Users\UserCoins;
use App\Models\Users\UserCoinPaymentStatus;
use App\Models\Users\UserCoinHistories;

class UserCoinHistoriesResource extends JsonResource
{
    public const RESOURCE_KEY_DATA = 'data';
    public const RESOURCE_KEY_TEXT = 'text';
    public const RESOURCE_KEY_VALUE = 'value';
    public const RESOURCE_KEY_HISTORY_VALUE = 'history_value';

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
                UserCoinHistories::UUID => $record[UserCoinHistories::UUID],
                self::RESOURCE_KEY_HISTORY_VALUE => $record[UserCoinHistories::TYPE],
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

    /**
     * Transform the resource into an array for create.
     *
     * @param int $userId ユーザーID
     * @param string $uuid UUID
     * @param int $type 履歴の種類
     * @param int $getFreeCoins 獲得無料コイン数
     * @param int $getPaidCoins 購入・獲得有料コイン数
     * @param int $getLimitedTimeCoins 購入・獲得期間限定コイン数
     * @param int $usedFreeCoins 消費無料コイン数
     * @param int $usedPaidCoins 消費有料コイン数
     * @param int $usedLimitedTimeCoins 消費期間限定コイン数
     * @param int $exipiredLimitedTimeCoins 期限切れコイン数
     * @param int|null $exipiredAt 期間限定コインの使用期限日時
     * @param string|null $orderId 注文ID
     * @param int $productId 製品ID
     * @param string $serviceId 決済サービスの決済ID
     * @return array
     */
    public static function toArrayForCreate(
        int $userId,
        string $uuid,
        int $type,
        int $getFreeCoins = 0,
        int $getPaidCoins = 0,
        int $getLimitedTimeCoins = 0,
        int $usedFreeCoins = 0,
        int $usedPaidCoins = 0,
        int $usedLimitedTimeCoins = 0,
        int $exipiredLimitedTimeCoins = 0,
        string|null $exipiredAt = null,
        string|null $orderId = null,
        int $productId = 0
    ): array {
        $dateTime = TimeLibrary::getCurrentDateTime();

        return [
            UserCoinHistories::USER_ID => $userId,
            UserCoinHistories::UUID => $uuid,
            UserCoinHistories::TYPE => $type,
            UserCoinHistories::GET_FREE_COINS => $getFreeCoins,
            UserCoinHistories::GET_PAID_COINS => $getPaidCoins,
            UserCoinHistories::GET_LIMITED_TIME_COINS => $getLimitedTimeCoins,
            UserCoinHistories::USED_FREE_COINS => $usedFreeCoins,
            UserCoinHistories::USED_PAID_COINS => $usedPaidCoins,
            UserCoinHistories::USED_LIMITED_TIME_COINS => $usedLimitedTimeCoins,
            UserCoinHistories::EXPIRED_LIMITED_TIME_COINS => $exipiredLimitedTimeCoins,
            UserCoinHistories::EXPIRED_AT => $exipiredAt,
            UserCoinHistories::OEDER_ID => $orderId,
            UserCoinHistories::PRODUCT_ID => $productId,
            UserCoinHistories::CREATED_AT => $dateTime,
            UserCoinHistories::UPDATED_AT => $dateTime,
            UserCoinHistories::DELETED_AT => null,
        ];
    }

    /**
     * Transform the resource into an array for update.
     *
     * @param int $userId ユーザーID
     * @param int $type 履歴の種類
     * @param int $getFreeCoins 獲得無料コイン数
     * @param int $getPaidCoins 購入・獲得有料コイン数
     * @param int $getLimitedTimeCoins 購入・獲得期間限定コイン数
     * @param int $usedFreeCoins 消費無料コイン数
     * @param int $usedPaidCoins 消費有料コイン数
     * @param int $usedLimitedTimeCoins 消費期間限定コイン数
     * @param int $exipiredLimitedTimeCoins 期限切れコイン数
     * @param int|null $exipiredAt 期間限定コインの使用期限日時
     * @param string|null $orderId 注文ID
     * @param int $productId 製品ID
     * @param string $serviceId 決済サービスの決済ID
     * @return array
     */
    public static function toArrayForUpdate(
        int $userId,
        int $type,
        int $getFreeCoins = 0,
        int $getPaidCoins = 0,
        int $getLimitedTimeCoins = 0,
        int $usedFreeCoins = 0,
        int $usedPaidCoins = 0,
        int $usedLimitedTimeCoins = 0,
        int $exipiredLimitedTimeCoins = 0,
        string|null $exipiredAt = null,
        string|null $orderId = null,
        int $productId = 0
    ): array {
        $dateTime = TimeLibrary::getCurrentDateTime();

        return [
            UserCoinHistories::USER_ID => $userId,
            UserCoinHistories::TYPE => $type,
            UserCoinHistories::GET_FREE_COINS => $getFreeCoins,
            UserCoinHistories::GET_PAID_COINS => $getPaidCoins,
            UserCoinHistories::GET_LIMITED_TIME_COINS => $getLimitedTimeCoins,
            UserCoinHistories::USED_FREE_COINS => $usedFreeCoins,
            UserCoinHistories::USED_PAID_COINS => $usedPaidCoins,
            UserCoinHistories::USED_LIMITED_TIME_COINS => $usedLimitedTimeCoins,
            UserCoinHistories::EXPIRED_LIMITED_TIME_COINS => $exipiredLimitedTimeCoins,
            UserCoinHistories::EXPIRED_AT => $exipiredAt,
            UserCoinHistories::OEDER_ID => $orderId,
            UserCoinHistories::PRODUCT_ID => $productId,
            UserCoinHistories::UPDATED_AT => $dateTime,
            UserCoinHistories::DELETED_AT => null,
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
            UserCoinHistories::UPDATED_AT => $dateTime,
            UserCoinHistories::DELETED_AT => $dateTime,
        ];
    }
}
