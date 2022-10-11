<?php

namespace App\Http\Requests\Admin\Events;

use Illuminate\Support\Facades\Config;
use App\Http\Requests\BaseRequest;

class EventBaseRequest extends BaseRequest
{
    // attribute keys
    public const KEY_ID           = 'id';
    public const KEY_NAME         = 'name';
    public const KEY_TYPE         = 'type';
    public const KEY_DETAIL       = 'detail';
    public const KEY_START_AT     = 'start_at';
    public const KEY_END_AT       = 'end_at';
    public const KEY_IMAGE        = 'image';
    public const KEY_EVENTS       = 'events';

    // attribute keys options
    public const KEY_FILE = 'file';

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->requestAuthorities = Config::get('myapp.executionRole.services.events');
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
            self::KEY_ID       => 'イベントID',
            self::KEY_NAME     => 'イベント名',
            self::KEY_TYPE     => '種類',
            self::KEY_DETAIL   => '詳細',
            self::KEY_START_AT => '公開開始日時',
            self::KEY_END_AT   => '公開終了日時',
            self::KEY_IMAGE    => 'イメージ',
            self::KEY_FILE     => 'ファイル',
        ];
    }
}
