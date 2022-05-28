<?php

namespace App\Http\Resources\Admins;

use Illuminate\Http\Resources\Json\JsonResource;

class RolesServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // レスポンス
        $response = [];

        foreach ($this->resource as $item) {
            $item->permissions = !$item->permissions ? [] : array_map(function ($permission) {
                return (int)$permission;
            }, explode(',', $item->permissions));
            $response['data'][] = $item;
        }

        return $response;
    }
}
