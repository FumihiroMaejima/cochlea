<?php

namespace App\Http\Requests\Admins;

use Illuminate\Foundation\Http\FormRequest;
use App\Repositories\Admins\Roles\RolesRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use App\Models\Roles;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Config;

class AdminCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return in_array($this->header(Config::get('myapp.headers.authority')), Config::get('myapp.executionRole.services.admins'), true);
    }

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

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'id'                    => 'id',
            'name'                  => '氏名',
            'email'                 => 'メールアドレス',
            'roleId'                => '権限',
            'password'              => 'パスワード',
            'password_confirmation' => '確認用パスワード'
        ];
    }

    /**
     * Handle a failed authorization attempt.
     *
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedAuthorization()
    {
        $response = [
            'status'  => 403,
            'errors'  => [],
            'message' => 'Forbidden'
        ];

        throw (new HttpResponseException(response()->json($response, 403)));
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        $response = [
            'status'  => 422,
            'errors'  => [],
            'message' => 'Unprocessable Entity'
        ];

        $response['errors'] = $validator->errors()->toArray();
        throw (new HttpResponseException(response()->json($response, 422)));
    }
}
