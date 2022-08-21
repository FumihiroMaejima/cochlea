<?php

namespace App\Http\Resources\Admins;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Config;
use App\Http\Requests\Admin\Roles\RoleCreateRequest;
use App\Http\Requests\Admin\Roles\RoleUpdateRequest;
use App\Library\Time\TimeLibrary;

class RolePermissionsResource extends JsonResource
{
    public const RESOURCE_KEY_NAME = 'name';
    public const RESOURCE_KEY_SHORT_NAME = 'short_name';
    public const RESOURCE_KEY_ROLE_ID = 'role_id';
    public const RESOURCE_KEY_PERMISSION_ID = 'permission_id';
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
        // insert用データ
        $data = [];
        $permissionsNameList = Config::get('myappSeeder.seeder.authority.permissionsNameList');

        foreach (range(0, (count($request->permissions) - 1)) as $i) {
            $row = [
                self::RESOURCE_KEY_NAME          => $request->name . '_' . $permissionsNameList[$i],
                self::RESOURCE_KEY_SHORT_NAME    => $permissionsNameList[$i],
                self::RESOURCE_KEY_ROLE_ID       => $this->resource->id,
                self::RESOURCE_KEY_PERMISSION_ID => $request->permissions[$i],
                self::RESOURCE_KEY_CREATED_AT    => $this->resource->created_at,
                self::RESOURCE_KEY_UPDATED_AT    => $this->resource->updated_at
            ];
            $data[] = $row;
        }
        return $data;
    }

    /**
     * Transform the resource into an array for create.
     *
     * @param RoleCreateRequest $request
     * @param object $role role record.
     * @return array
     */
    public static function toArrayForCreate(RoleCreateRequest $request, object $role): array
    {
        // insert用データ
        $data = [];
        $permissionsNameList = Config::get('myappSeeder.seeder.authority.permissionsNameList');

        foreach (range(0, (count($request->permissions) - 1)) as $i) {
            $row = [
                self::RESOURCE_KEY_NAME          => $request->name . '_' . $permissionsNameList[$i],
                self::RESOURCE_KEY_SHORT_NAME    => $permissionsNameList[$i],
                self::RESOURCE_KEY_ROLE_ID       => $role->id,
                self::RESOURCE_KEY_PERMISSION_ID => $request->permissions[$i],
                self::RESOURCE_KEY_CREATED_AT    => $role->created_at,
                self::RESOURCE_KEY_UPDATED_AT    => $role->updated_at
            ];
            $data[] = $row;
        }
        return $data;
    }

    /**
     * Transform the resource into an array for update.
     *
     * @param RoleUpdateRequest $request
     * @return array
     */
    public static function toArrayForUpdate(RoleUpdateRequest $request): array
    {
        // insert用データ
        $data = [];

        $dateTime = TimeLibrary::getCurrentDateTime();

        $permissionsNameList = Config::get('myappSeeder.seeder.authority.permissionsNameList');

        foreach (range(0, (count($request->permissions) - 1)) as $i) {
            $row = [
                self::RESOURCE_KEY_NAME          => $request->name . '_' . $permissionsNameList[$i],
                self::RESOURCE_KEY_SHORT_NAME    => $permissionsNameList[$i],
                self::RESOURCE_KEY_ROLE_ID       => $request->id,
                self::RESOURCE_KEY_PERMISSION_ID => $request->permissions[$i],
                self::RESOURCE_KEY_CREATED_AT    => $dateTime,
                self::RESOURCE_KEY_UPDATED_AT    => $dateTime
            ];
            $data[] = $row;
        }
        return $data;
    }

    /**
     * Transform the resource into an array for delete by update request.
     *
     * @param RoleUpdateRequest $request
     * @return array
     */
    public static function toArrayForDeleteByUpdateResource(RoleUpdateRequest $request)
    {
        $dateTime = TimeLibrary::getCurrentDateTime();

        return [
            self::RESOURCE_KEY_NAME       => $request->name . '_' . $dateTime,
            self::RESOURCE_KEY_UPDATED_AT => $dateTime,
            self::RESOURCE_KEY_DELETED_AT => $dateTime
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
}
