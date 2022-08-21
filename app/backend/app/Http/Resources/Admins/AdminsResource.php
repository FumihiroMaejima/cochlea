<?php

namespace App\Http\Resources\Admins;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Admin\Admins\AdminCreateRequest;
use App\Http\Requests\Admin\Admins\AdminUpdateRequest;
use App\Models\Masters\Admins;
use App\Library\Time\TimeLibrary;

class AdminsResource extends JsonResource
{
    public const RESOURCE_KEY_DATA = 'data';

    public const RESOURCE_KEY_NAME = 'name';
    public const RESOURCE_KEY_EMAIL = 'email';
    public const RESOURCE_KEY_PASSWORD = 'password';
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
        $test = $this->resource;
        // $this->resourceはcollection
        // $this->resource->itemsは検索結果の各レコードをstdClassオブジェクトとして格納した配列
        return [
            self::RESOURCE_KEY_DATA => $this->resource->toArray($request)
        ];
    }

    /**
     * Transform the resource into an array for create.
     *
     * @param AdminCreateRequest $request
     * @return array
     */
    public static function toArrayForCreate(AdminCreateRequest $request): array
    {
        /* $carbon = new Carbon();
        $test = $carbon->now()->format('Y-m-d H:i:s'); */
        $dateTime = TimeLibrary::getCurrentDateTime();

        return [
            self::RESOURCE_KEY_NAME       => $request->name,
            self::RESOURCE_KEY_EMAIL      => $request->email,
            self::RESOURCE_KEY_PASSWORD   => Hash::make($request->password),
            self::RESOURCE_KEY_CREATED_AT => $dateTime,
            self::RESOURCE_KEY_UPDATED_AT => $dateTime
        ];
    }

    /**
     * Transform the resource into an array for update.
     *
     * @param AdminUpdateRequest $request
     * @return array
     */
    public static function toArrayForUpdate(AdminUpdateRequest $request): array
    {
        $dateTime = TimeLibrary::getCurrentDateTime();

        return [
            self::RESOURCE_KEY_NAME       => $request->name,
            self::RESOURCE_KEY_EMAIL      => $request->email,
            self::RESOURCE_KEY_UPDATED_AT => $dateTime
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

    /**
     * Transform the resource into an array for update.
     *
     * @param string $newPassword new password
     * @return array
     */
    public static function toArrayForUpdatePassword(string $newPassword): array
    {
        $dateTime = TimeLibrary::getCurrentDateTime();

        return [
            Admins::PASSWORD   => bcrypt($newPassword),
            Admins::UPDATED_AT => $dateTime
        ];
    }
}
