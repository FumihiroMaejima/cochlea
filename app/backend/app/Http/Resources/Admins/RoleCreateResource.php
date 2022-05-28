<?php

namespace App\Http\Resources\Admins;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class RoleCreateResource extends JsonResource
{
    public const RESOURCE_KEY_NAME = 'name';
    public const RESOURCE_KEY_CODE = 'code';
    public const RESOURCE_KEY_DETAIL = 'detail';
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
        /* $carbon = new Carbon();
        $test = $carbon->now()->format('Y-m-d H:i:s'); */
        $dateTime = Carbon::now()->format('Y-m-d H:i:s');

        return [
            self::RESOURCE_KEY_NAME        => $request->name,
            self::RESOURCE_KEY_CODE        => $request->code,
            self::RESOURCE_KEY_DETAIL      => $request->detail,
            self::RESOURCE_KEY_CREATED_AT  => $dateTime,
            self::RESOURCE_KEY_UPDATED_AT  => $dateTime
        ];
    }
}
