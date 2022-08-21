<?php

namespace App\Http\Requests\Admin\Admins;

use Illuminate\Support\Facades\Config;
use App\Http\Requests\BaseRequest;


class AdminBaseRequest extends BaseRequest
{
    // attribute keys
    protected const KEY_ID                    = 'id';
    protected const KEY_NAME                  = 'name';
    protected const KEY_EMAIL                 = 'email';
    protected const KEY_ROLE_ID               = 'roleId';
    protected const KEY_PASSWORD              = 'password';
    protected const KEY_PASSWORD_CONFIRMATION = 'password_confirmation';
    protected const KEY_TEL                   = 'tel';
    protected const KEY_FILE                  = 'file';
    protected const KEY_IMAGE                 = 'image';

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->requestAuthorities = Config::get('myapp.executionRole.services.admins');
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
            self::KEY_ID                    => 'id',
            self::KEY_NAME                  => '氏名',
            self::KEY_EMAIL                 => 'メールアドレス',
            self::KEY_ROLE_ID               => '権限',
            self::KEY_PASSWORD              => 'パスワード',
            self::KEY_PASSWORD_CONFIRMATION => '確認用パスワード'
        ];
    }
}
