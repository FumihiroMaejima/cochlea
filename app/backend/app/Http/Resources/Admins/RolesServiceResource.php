<?php

namespace App\Http\Resources\Admins;

use Illuminate\Http\Resources\Json\JsonResource;

class RolesServiceResource extends JsonResource
{
    public const RESOURCE_KEY_DATA = 'data';

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
            $response[self::RESOURCE_KEY_DATA][] = $item;
        }

        return $response;
    }
}
