<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Admins;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Admin\Admins\AdminBaseRequest;
use App\Http\Requests\BaseRequest;
use App\Models\Masters\Roles;
use App\Repositories\Masters\Roles\RolesRepositoryInterface;

class AdminCreateRequest extends AdminBaseRequest
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
        $roleModel = app()->make(Roles::class);

        return [
            'name'   => 'required|string|between:1,50',
            'email'  => 'required|string|email:rfc|between:1,50',
            // 'email' => ['regex:/^.+@.+$/i']
            'roleId' => 'required|integer|exists:' . $roleModel->getTable() . ',id',
            'password'   => 'required|string|between:8,100|confirmed',
            'password_confirmation'   => 'same:password',
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
            'email.email' => ':attributeの形式が正しくありません。',
            'required'    => ':attributeは必須項目です。',
            'string'      => ':attributeは文字列を入力してください。',
            'between'     => ':attributeは:min〜:max文字以内で入力してください。',
            'confirmed'     => ':attributeは確認用にもう一度入力してください。',
            'same'     => ':attributeは同一の値ではありません。'
            // 'email' => 'アルファベット半角で入力してください。'
            // 'tel.regex' => '「000-0000-0000」の形式で入力してください。'
        ];
    }
}
