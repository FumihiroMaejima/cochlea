<?php

declare(strict_types=1);

namespace App\Http\Resources\Admins;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AdminsCollection extends ResourceCollection
{
    public const RESOURCE_KEY_DATA = 'data';

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
        $test = $this->resource;

        // $this->resourceはCollection
        // 各itemは1レコードずつのデータを持つAdminsResourceクラス
        foreach ($this->resource as $item) {
            // 各itemのresourceはstdClassオブジェクトの１レコード分のデータ
            $response[] = $item->resource;
        }

        return $response;
    }
}
