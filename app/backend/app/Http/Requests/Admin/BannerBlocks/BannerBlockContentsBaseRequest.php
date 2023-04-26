<?php

namespace App\Http\Requests\Admin\BannerBlocks;

use Illuminate\Support\Facades\Config;
use App\Http\Requests\BaseRequest;

class BannerBlockContentsBaseRequest extends BaseRequest
{
    // attribute keys
    public const KEY_ID              = 'id';
    public const KEY_BANNER_BLOCK_ID = 'banner_block_id';
    public const KEY_BANNER_ID       = 'banner_id';
    public const KEY_TYPE            = 'type';
    public const KEY_ORDER           = 'order';
    public const KEY_START_AT        = 'start_at';
    public const KEY_END_AT          = 'end_at';
    public const KEY_IMAGE           = 'image';
    public const KEY_HOME_CONTENTS   = 'homeContents';

    // attribute keys options
    public const KEY_FILE = 'file';

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->requestAuthorities = Config::get('myapp.executionRole.services.home');
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
            self::KEY_ID              => 'ID',
            self::KEY_BANNER_BLOCK_ID => 'バナーブロックID',
            self::KEY_BANNER_ID       => 'バナーID',
            self::KEY_TYPE            => 'タイプ',
            self::KEY_ORDER           => '順番',
            self::KEY_START_AT        => '公開開始日時',
            self::KEY_END_AT          => '公開終了日時',
            self::KEY_IMAGE           => 'イメージ',
            self::KEY_FILE            => 'ファイル',
        ];
    }
}
