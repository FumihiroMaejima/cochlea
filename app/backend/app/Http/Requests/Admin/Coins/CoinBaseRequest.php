<?php

namespace App\Http\Requests\Admin\Coins;

use Illuminate\Support\Facades\Config;
use App\Http\Requests\BaseRequest;

class CoinBaseRequest extends BaseRequest
{
    // attribute keys
    public const KEY_ID       = 'id';
    public const KEY_NAME     = 'name';
    public const KEY_DETAIL   = 'detail';
    public const KEY_PRICE    = 'price';
    public const KEY_COST     = 'cost';
    public const KEY_START_AT = 'start_at';
    public const KEY_END_AT   = 'end_at';
    public const KEY_IMAGE    = 'image';
    public const KEY_COINS    = 'coins';

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->requestAuthorities = Config::get('myapp.executionRole.services.coins');
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
            self::KEY_ID       => 'コインID',
            self::KEY_NAME     => 'コイン名',
            self::KEY_DETAIL   => '詳細',
            self::KEY_PRICE    => 'コインの購入価格',
            self::KEY_COST     => 'アプリケーション内のコインの価格',
            self::KEY_START_AT => '公開開始日時',
            self::KEY_END_AT   => '公開終了日時',
            self::KEY_IMAGE    => 'イメージ',
        ];
    }
}
