<?php

namespace App\Http\Resources\Admins;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use App\Library\Time\TimeLibrary;

class AdminsRolesResource extends JsonResource
{
    public const RESOURCE_KEY_ROLE_ID = 'role_id';
    public const RESOURCE_KEY_ADMIN_ID = 'admin_id';
    public const RESOURCE_KEY_CREATED_AT = 'created_at';
    public const RESOURCE_KEY_UPDATED_AT = 'updated_at';
    public const RESOURCE_KEY_DELETED_AT = 'deleted_at';

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $dateTime = TimeLibrary::getCurrentDateTime();

        return [
            self::RESOURCE_KEY_ROLE_ID    => $request->roleId,
            self::RESOURCE_KEY_UPDATED_AT => $dateTime
        ];
    }

    /**
     * Transform the resource into an array for create.
     *
     * @param Request $request
     * @param object $admin
     * @return array
     */
    public static function toArrayForCreate(Request $request, object $admin): array
    {
        return [
            self::RESOURCE_KEY_ROLE_ID    => $request->roleId,
            self::RESOURCE_KEY_ADMIN_ID   => $admin->id,
            self::RESOURCE_KEY_CREATED_AT => $admin->created_at,
            self::RESOURCE_KEY_UPDATED_AT => $admin->updated_at
            // self::RESOURCE_KEY_ADMIN_ID   => $this->resource->id,
            // self::RESOURCE_KEY_CREATED_AT => $this->resource->created_at,
            // self::RESOURCE_KEY_UPDATED_AT => $this->resource->updated_at
        ];
    }

    /**
     * Transform the resource into an array for update.
     *
     * @param Request $request
     * @return array
     */
    public static function toArrayForUpdate(Request $request): array
    {
        $dateTime = TimeLibrary::getCurrentDateTime();

        return [
            self::RESOURCE_KEY_ROLE_ID    => $request->roleId,
            self::RESOURCE_KEY_UPDATED_AT => $dateTime
        ];
    }

    /**
     * Transform the resource into an array for delete.
     *
     * @param Request  $request
     * @return array
     */
    public static function toArrayForDelete(Request $request): array
    {
        $dateTime = TimeLibrary::getCurrentDateTime();

        return [
            self::RESOURCE_KEY_ADMIN_ID   => $request->id,
            self::RESOURCE_KEY_UPDATED_AT => $dateTime,
            self::RESOURCE_KEY_DELETED_AT => $dateTime
        ];
    }
}
