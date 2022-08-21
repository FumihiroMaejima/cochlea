<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\BaseRequest;
use App\Models\Masters\Roles;
use App\Repositories\Admins\Roles\RolesRepositoryInterface;

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
