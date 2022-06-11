<?php

namespace App\Http\Resources\Admins;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class AdminUpdateResource extends JsonResource
{
    public const RESOURCE_KEY_NAME = 'name';
    public const RESOURCE_KEY_EMAIL = 'email';
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
            self::RESOURCE_KEY_NAME       => $request->name,
            self::RESOURCE_KEY_EMAIL      => $request->email,
            self::RESOURCE_KEY_UPDATED_AT => $dateTime
        ];
    }
}
