<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use App\Library\Time\TimeLibrary;
use App\Models\User;

class UsersResource extends JsonResource
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
     * @param int $userId ユーザ-ID
     * @param string $name ユーザー名
     * @param string $email メールアドレス
     * @param string $password パスワード
     * @return array
     */
    public static function toArrayForCreate(
        int $userId,
        string $name,
        string $email,
        string $password
    ): array {
        $dateTime = TimeLibrary::getCurrentDateTime();

        return [
            User::ID => $userId,
            User::NAME => $name,
            User::EMAIL => $email,
            User::PASSWORD => bcrypt($password),
            User::CREATED_AT => $dateTime,
            User::UPDATED_AT => $dateTime,
            User::DELETED_AT => null,
        ];
    }

    /**
     * Transform the resource into an array for update.
     *
     * @param int $userId ユーザーID
     * @param string $name ユーザー名
     * @param string $email メールアドレス
     * @return array
     */
    public static function toArrayForUpdate(
        int $userId,
        string $name,
        string $email
    ): array {
        return [
            User::ID => $userId,
            User::NAME => $name,
            User::EMAIL => $email,
            User::UPDATED_AT => TimeLibrary::getCurrentDateTime(),
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
            User::UPDATED_AT => $dateTime,
            User::DELETED_AT => $dateTime,
        ];
    }
}
