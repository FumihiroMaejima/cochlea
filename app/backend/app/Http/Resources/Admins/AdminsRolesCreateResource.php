<?php

namespace App\Http\Resources\Admins;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminsRolesCreateResource extends JsonResource
{
    public const RESOURCE_KEY_ROLE_ID = 'role_id';
    public const RESOURCE_KEY_ADMIN_ID = 'admin_id';
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
        return [
            self::RESOURCE_KEY_ROLE_ID    => $request->roleId,
            self::RESOURCE_KEY_ADMIN_ID   => $this->resource->id,
            self::RESOURCE_KEY_CREATED_AT => $this->resource->created_at,
            self::RESOURCE_KEY_UPDATED_AT => $this->resource->updated_at
        ];
    }
}
