<?php

namespace App\Http\Resources\Admins;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Config;

class RolePermissionsCreateResource extends JsonResource
{
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
        $permissionsNameList = Config::get('myapp.seeder.authority.permissionsNameList');

        foreach (range(0, (count($request->permissions) - 1)) as $i) {
            $row = [
                'name'          => $request->name . '_' . $permissionsNameList[$i],
                'short_name'    => $permissionsNameList[$i],
                'role_id'       => $this->resource->id,
                'permission_id' => $request->permissions[$i],
                'created_at'    => $this->resource->created_at,
                'updated_at'    => $this->resource->updated_at
            ];
            $data[] = $row;
        }
        return $data;
    }
}
