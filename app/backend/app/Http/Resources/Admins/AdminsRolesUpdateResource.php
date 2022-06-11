<?php

namespace App\Http\Resources\Admins;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class AdminsRolesUpdateResource extends JsonResource
{
    public const RESOURCE_KEY_ROLE_ID = 'role_id';
    public const RESOURCE_KEY_UPDATED_AT = 'updated_at';

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $dateTime = Carbon::now()->format('Y-m-d H:i:s');
        return [
            self::RESOURCE_KEY_ROLE_ID    => $request->roleId,
            self::RESOURCE_KEY_UPDATED_AT => $dateTime
        ];
    }
}
