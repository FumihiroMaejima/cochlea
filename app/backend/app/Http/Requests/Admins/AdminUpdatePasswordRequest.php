<?php

namespace App\Http\Requests\Admins;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\BaseRequest;
use App\Models\Masters\Roles;
use App\Repositories\Admins\Roles\RolesRepositoryInterface;

class AdminUpdatePasswordRequest extends BaseRequest
{
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
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // ルーティングで設定しているidパラメーターをリクエストデータとして設定する
        $this->merge(['id' => $this->route('id')]);
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
            'currentPassword'      => 'required|string|between:8,100',
            'newPassword'          => 'required|string|between:8,100',
            'passwordConfirmation' => 'same:newPassword',
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
            'required'    => ':attributeは必須項目です。',
            'string'      => ':attributeは文字列を入力してください。',
            'between'     => ':attributeは:min文字以上で入力してください。',
            'confirmed'   => ':attributeは確認用にもう一度入力してください。',
            'same'        => ':attributeは同一の値ではありません。'
            // 'email' => 'アルファベット半角で入力してください。'
            // 'tel.regex' => '「000-0000-0000」の形式で入力してください。'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'currentPassword'      => '旧パスワード',
            'newPassword'          => '新パスワード',
            'passwordConfirmation' => '確認用パスワード',
        ];
    }
}
