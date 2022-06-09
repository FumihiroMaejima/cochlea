<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Repositories\Admins\Roles\RolesRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use App\Models\Roles;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Config;

class BaseRequest extends FormRequest
{
    private const AUTHORIZATION_ERROR_STATUS_CODE = 403;
    private const AUTHORIZATION_ERROR_MESSAGE = 'Forbidden';
    private const VALIDATION_ERROR_STATUS_CODE = 422;
    private const VALIDATION_ERROR_MESSAGE= 'Unprocessable Entity';

    // error response key
    private const ERROR_RESPONSE_KEY_STATUS = 'status';
    private const ERROR_RESPONSE_KEY_ERRORS = 'errors';
    private const ERROR_RESPONSE_KEY_MESSAGE = 'message';

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
            self::ERROR_RESPONSE_KEY_STATUS  => self::AUTHORIZATION_ERROR_STATUS_CODE,
            self::ERROR_RESPONSE_KEY_ERRORS  => [],
            self::ERROR_RESPONSE_KEY_MESSAGE => self::AUTHORIZATION_ERROR_MESSAGE
        ];

        throw (new HttpResponseException(response()->json($response, self::AUTHORIZATION_ERROR_STATUS_CODE)));
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
            self::ERROR_RESPONSE_KEY_STATUS  => self::VALIDATION_ERROR_STATUS_CODE,
            self::ERROR_RESPONSE_KEY_ERRORS  => [],
            self::ERROR_RESPONSE_KEY_MESSAGE => self::VALIDATION_ERROR_MESSAGE
        ];

        $response[self::ERROR_RESPONSE_KEY_ERRORS] = $validator->errors()->toArray();
        throw (new HttpResponseException(response()->json($response, self::VALIDATION_ERROR_STATUS_CODE)));
    }
}
