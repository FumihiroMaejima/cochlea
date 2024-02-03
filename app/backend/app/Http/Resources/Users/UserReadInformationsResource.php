<?php

declare(strict_types=1);

namespace App\Http\Resources\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use App\Library\Time\TimeLibrary;
use App\Models\Users\UserReadInformations;

class UserReadInformationsResource extends JsonResource
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
     * Transform the resource into an array for create.
     *
     * @param int $userId ユーザーID
     * @param int $informationId お知らせID
     * @return array
     */
    public static function toArrayForCreate(int $userId, int $informationId): array
    {
        $dateTime = TimeLibrary::getCurrentDateTime();

        return [
            UserReadInformations::USER_ID => $userId,
            UserReadInformations::INFORMATION_ID => $informationId,
            UserReadInformations::CREATED_AT => $dateTime,
            UserReadInformations::UPDATED_AT => $dateTime,
            UserReadInformations::DELETED_AT => null,
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
            UserReadInformations::UPDATED_AT => $dateTime,
            UserReadInformations::DELETED_AT => $dateTime,
        ];
    }
}
