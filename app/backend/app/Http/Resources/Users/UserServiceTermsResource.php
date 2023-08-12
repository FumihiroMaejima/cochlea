<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use App\Library\Time\TimeLibrary;
use App\Models\Users\UserReadInformations;
use App\Models\Users\UserServiceTerms;

class UserServiceTermsResource extends JsonResource
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
     * @param int $serviceTermId 利用規約ID
     * @return array
     */
    public static function toArrayForCreate(int $userId, int $serviceTermId): array
    {
        $dateTime = TimeLibrary::getCurrentDateTime();

        return [
            UserServiceTerms::USER_ID => $userId,
            UserServiceTerms::SERVICE_TERM_ID => $serviceTermId,
            UserServiceTerms::CREATED_AT => $dateTime,
            UserServiceTerms::UPDATED_AT => $dateTime,
            UserServiceTerms::DELETED_AT => null,
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
            UserServiceTerms::UPDATED_AT => $dateTime,
            UserServiceTerms::DELETED_AT => $dateTime,
        ];
    }
}
