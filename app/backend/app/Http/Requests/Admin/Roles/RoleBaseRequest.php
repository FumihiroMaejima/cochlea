<?php

namespace App\Http\Requests\Admin\Roles;

use Illuminate\Support\Facades\Config;
use App\Http\Requests\BaseRequest;

class RoleBaseRequest extends BaseRequest
{
    // attribute keys
    public const KEY_ID          = 'id';
    public const KEY_NAME        = 'name';
    public const KEY_CODE        = 'code';
    public const KEY_DETAIL      = 'detail';
    public const KEY_PERMISSIONS = 'permissions';
    public const KEY_ROLES       = 'roles';

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->requestAuthorities = Config::get('myapp.executionRole.services.roles');
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
            self::KEY_NAME        => 'ロール名',
            self::KEY_CODE        => 'ロールコード',
            self::KEY_DETAIL      => '詳細',
            self::KEY_PERMISSIONS => 'パーミッション',
            self::KEY_ROLES       => 'ロール',
        ];
    }
}
