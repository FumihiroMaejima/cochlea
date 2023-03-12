<?php

namespace App\Http\Requests\Admin\Banners;

use Illuminate\Support\Facades\Config;
use App\Http\Requests\BaseRequest;

class BannerBaseRequest extends BaseRequest
{
    // attribute keys
    public const KEY_ID        = 'id';
    public const KEY_UUID      = 'uuid';
    public const KEY_NAME      = 'name';
    public const KEY_DETAIL    = 'detail';
    public const KEY_LOCATION  = 'location';
    public const KEY_PC_HEIGHT = 'pc_height';
    public const KEY_PC_WIDTH  = 'pc_width';
    public const KEY_SP_HEIGHT = 'sp_height';
    public const KEY_SP_WIDTH  = 'sp_width';
    public const KEY_START_AT  = 'start_at';
    public const KEY_END_AT    = 'end_at';
    public const KEY_URL       = 'url';
    public const KEY_IMAGE     = 'image';
    public const KEY_BANNERS   = 'banners';

    // attribute keys options
    public const KEY_FILE = 'file';

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->requestAuthorities = Config::get('myapp.executionRole.services.banners');
        return parent::authorize();
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            self::KEY_ID        => 'バナーID',
            self::KEY_UUID      => 'UUID',
            self::KEY_NAME      => 'バナー名',
            self::KEY_DETAIL    => '詳細',
            self::KEY_LOCATION  => '設置場所',
            self::KEY_PC_HEIGHT => 'PCでの高さ',
            self::KEY_PC_WIDTH  => 'PCでの幅',
            self::KEY_SP_HEIGHT => 'SPでの高さ',
            self::KEY_SP_WIDTH  => 'SPでの幅',
            self::KEY_START_AT  => '公開開始日時',
            self::KEY_END_AT    => '公開終了日時',
            self::KEY_URL       => 'url',
            self::KEY_IMAGE     => 'イメージ',
            self::KEY_FILE      => 'ファイル',
        ];
    }
}
