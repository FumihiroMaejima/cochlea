<?php

namespace App\Http\Resources\Admins;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RolesCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // レスポンス
        $response = [];

        // $this->resourceはCollection
        // 各itemは1レコードずつのデータを持つRolesResourceクラス
        foreach ($this->resource as $item) {
            // 各itemのresourceはstdClassオブジェクトの１レコード分のデータ
            $role = [
                'text' => $item->resource->name,
                'value' => $item->resource->id,
            ];
            // 多次元配列の中の連想配列を格納
            $response['data'][] = $role;
        }

        return $response;
    }
}
