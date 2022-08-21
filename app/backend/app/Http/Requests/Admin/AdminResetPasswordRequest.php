<?php

namespace App\Http\Requests\Admins;

use Illuminate\Support\Facades\Config;
use App\Http\Requests\BaseRequest;
use App\Models\Masters\Roles;

class AdminResetPasswordRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->header(Config::get('myapp.headers.passwordReset'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'password'             => 'required|string|between:8,100',
            'passwordConfirmation' => 'same:password',
            'token'                => 'required|string',
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
            'token.required' => 'パスワードのリセットに失敗しました。',
            'token.string' => 'パスワードのリセットに失敗しました。',
            'required'    => ':attributeは必須項目です。',
            'string'      => ':attributeは文字列を入力してください。',
            'between'     => ':attributeは:min文字以上で入力してください。',
            'confirmed'   => ':attributeは確認用にもう一度入力してください。',
            'same'        => ':attributeは同一の値ではありません。'
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
            'password'             => 'パスワード',
            'passwordConfirmation' => '確認用パスワード',
            'token'                => 'パスワードリセットトークン',
        ];
    }
}
