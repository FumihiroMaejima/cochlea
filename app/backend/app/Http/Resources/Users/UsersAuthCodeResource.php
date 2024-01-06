<?php

declare(strict_types=1);

namespace App\Http\Resources\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use App\Library\Time\TimeLibrary;
use App\Models\Users\UserAuthCodes;

class UsersAuthCodeResource extends JsonResource
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
     * @param int $type 認証種類
     * @param int $code コード
     * @param int $count 回数
     * @param int $isUsed 使用済みか
     * @param string $expiredAt
     * @return array
     */
    public static function toArrayForCreate(
        int $userId,
        int $type,
        int $code,
        int $count,
        int $isUsed,
        string $expiredAt
    ): array {
        $dateTime = TimeLibrary::getCurrentDateTime();

        return [
            UserAuthCodes::USER_ID => $userId,
            UserAuthCodes::TYPE => $type,
            UserAuthCodes::CODE => $code,
            UserAuthCodes::COUNT => $count,
            UserAuthCodes::IS_USED => $isUsed,
            UserAuthCodes::EXPIRED_AT => $expiredAt,
            UserAuthCodes::CREATED_AT => $dateTime,
            UserAuthCodes::UPDATED_AT => $dateTime
        ];
    }

    /**
     * Transform the resource into an array for update.
     *
     * @param int $userId ユーザーID
     * @param int $type 認証種類
     * @param int $code コード
     * @param int $count 回数
     * @param int $isUsed 使用済みか
     * @return array
     */
    public static function toArrayForUpdate(
        int $userId,
        int $type,
        int $code,
        int $count,
        int $isUsed
    ): array {
        return [
            UserAuthCodes::USER_ID => $userId,
            UserAuthCodes::TYPE => $type,
            UserAuthCodes::CODE => $code,
            UserAuthCodes::COUNT => $count,
            UserAuthCodes::IS_USED => $isUsed,
            UserAuthCodes::UPDATED_AT => TimeLibrary::getCurrentDateTime()
        ];
    }

    /**
     * Transform the resource into an array for delete.
     *
     * @return array
     */
    public static function toArrayForDelete(): array
    {
        return [
            UserAuthCodes::IS_USED => 1,
            UserAuthCodes::UPDATED_AT => TimeLibrary::getCurrentDateTime(),
        ];
    }
}
