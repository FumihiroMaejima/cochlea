<?php

namespace App\Http\Resources\Admins;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Config;

class RolePermissionsCreateResource extends JsonResource
{
    public const RESOURCE_KEY_NAME = 'name';
    public const RESOURCE_KEY_SHORT_NAME = 'short_name';
    public const RESOURCE_KEY_ROLE_ID = 'role_id';
    public const RESOURCE_KEY_PERMISSION_ID = 'permission_id';
    public const RESOURCE_KEY_CREATED_AT = 'created_at';
    public const RESOURCE_KEY_UPDATED_AT = 'updated_at';

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
}
