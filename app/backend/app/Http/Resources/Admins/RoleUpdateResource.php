<?php

namespace App\Http\Resources\Admins;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class RoleUpdateResource extends JsonResource
{
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
            'name'        => $request->name,
            'code'        => $request->code,
            'detail'      => $request->detail,
            'updated_at'  => $dateTime
        ];
    }
}
