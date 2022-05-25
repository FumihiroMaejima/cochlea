<?php

namespace App\Http\Resources\Admins;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class RolePermissionsUpdateResource extends JsonResource
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
        $dateTime = Carbon::now()->format('Y-m-d H:i:s');
        $permissionsNameList = Config::get('myapp.seeder.authority.permissionsNameList');

        foreach (range(0, (count($request->permissions) - 1)) as $i) {
            $row = [
                'name'          => $request->name . '_' . $permissionsNameList[$i],
                'short_name'    => $permissionsNameList[$i],
                'role_id'       => $request->id,
                'permission_id' => $request->permissions[$i],
                'created_at'    => $dateTime,
                'updated_at'    => $dateTime
            ];
            $data[] = $row;
        }
        return $data;
    }
}
