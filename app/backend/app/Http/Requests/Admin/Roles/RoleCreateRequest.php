<?php

namespace App\Http\Requests\Admin\Roles;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Collection;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Http\Requests\Admin\Roles\RoleBaseRequest;
use App\Http\Requests\BaseRequest;
use App\Models\Masters\Permissions;

class RoleCreateRequest extends RoleBaseRequest
{
    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // ルーティングで設定しているidパラメーターをリクエストデータとして設定する
        // $this->merge(['id' => $this->route('id')]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $permissionsModel = app()->make(Permissions::class);

        return [
            'name'        => 'required|string|between:1,50',
            'code'        => 'required|string|between:1,50',
            'detail'      => 'required|string|between:1,100',
            'permissions' => 'required|array|exists:' . $permissionsModel->getTable() . ',id'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'required'   => ':attributeは必須項目です。',
            'string'     => ':attributeは文字列を入力してください。',
            'array'      => ':attributeは配列で入力してください。',
            'between'    => ':attributeは:min〜:max文字以内で入力してください。',
            'exists'     => '指定した:attributeは存在しません。'
        ];
    }
}
