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

    // attribute keys
    private const ATTRIBUTE_ID                    = 'id';
    private const ATTRIBUTE_NAME                  = 'name';
    private const ATTRIBUTE_EMAIL                 = 'email';
    private const ATTRIBUTE_ROLE_ID               = 'roleId';
    private const ATTRIBUTE_PASSWORD              = 'password';
    private const ATTRIBUTE_PASSWORD_CONFIRMATION = 'password_confirmation';

    // attribute name
    private const ATTRIBUTE_NAME_ID                    = 'id';
    private const ATTRIBUTE_NAME_NAME                  = '氏名';
    private const ATTRIBUTE_NAME_EMAIL                 = 'メールアドレス';
    private const ATTRIBUTE_NAME_ROLE_ID               = '権限';
    private const ATTRIBUTE_NAME_PASSWORD              = 'パスワード';
    private const ATTRIBUTE_NAME_PASSWORD_CONFIRMATION = '確認用パスワード';

    // rules
    private const RULE_NAME = 'required|string|between:1,50';
    private const RULE_EMAIL = 'required|string|email:rfc|between:1,50';
    private const RULE_ROLE_ID = 'required|integer|exists:'; // model のidカラムを指定(. $roleModel->getTable() . ',id')
    private const RULE_PASSWORD = 'required|string|between:8,100|confirmed';
    private const RULE_PASSWORD_CONFIRMATION = 'same:password';

    // rule key
    private const RULE_KEY_EMAIL_EMAIL = 'email.email';
    private const RULE_KEY_REQUIRED = 'required';
    private const RULE_KEY_STRING = 'string';
    private const RULE_KEY_BETWEEN = 'between';
    private const RULE_KEY_CONFIRMED = 'confirmed';
    private const RULE_KEY_SAME = 'same';
    private const RULE_KEY_EMAIL = 'email';
    private const RULE_KEY_TEL_REGEX = 'tel.regex';

    // message
    private const RULE_KEY_MESSAGE_EMAIL_EMAIL = ':attributeの形式が正しくありません。';
    private const RULE_KEY_MESSAGE_REQUIRED = ':attributeは必須項目です。';
    private const RULE_KEY_MESSAGE_STRING = ':attributeは文字列を入力してください。';
    private const RULE_KEY_MESSAGE_BETWEEN = ':attributeは:min〜:max文字以内で入力してください。';
    private const RULE_KEY_MESSAGE_CONFIRMED = ':attributeは確認用にもう一度入力してください。';
    private const RULE_KEY_MESSAGE_SAME = ':attributeは同一の値ではありません。';
    private const RULE_KEY_MESSAGE_EMAIL = 'アルファベット半角で入力してください。';
    private const RULE_KEY_MESSAGE_TEL_REGEX = '「000-0000-0000」の形式で入力してください。';

    // authority
    private const NO_AUTHORITIES_COUNT = 0;

    /** @var array $requestAuthorities approved autorities in this requst */
    private array $requestAuthorities = [];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (count($this->requestAuthorities) === self::NO_AUTHORITIES_COUNT) {
            return true;
        }
        // if has authorities
        // $this->requestAuthorities = Config::get('myapp.executionRole.services.admins');
        // return $this->checkRequestAuthority($this->requestAuthorities);
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
            self::ATTRIBUTE_NAME   => self::RULE_NAME,
            self::ATTRIBUTE_EMAIL  => self::RULE_EMAIL,
            // 'email' => ['regex:/^.+@.+$/i']
            self::ATTRIBUTE_ROLE_ID => self::RULE_ROLE_ID . $roleModel->getTable() . ',id',
            self::ATTRIBUTE_PASSWORD   => self::RULE_PASSWORD,
            self::ATTRIBUTE_PASSWORD_CONFIRMATION   => self::RULE_PASSWORD_CONFIRMATION,
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
            self::RULE_KEY_EMAIL_EMAIL => self::RULE_KEY_MESSAGE_EMAIL_EMAIL,
            self::RULE_KEY_REQUIRED    => self::RULE_KEY_MESSAGE_REQUIRED,
            self::RULE_KEY_STRING      => self::RULE_KEY_MESSAGE_STRING,
            self::RULE_KEY_BETWEEN     => self::RULE_KEY_MESSAGE_BETWEEN,
            self::RULE_KEY_CONFIRMED     => self::RULE_KEY_MESSAGE_CONFIRMED,
            self::RULE_KEY_SAME     => self::RULE_KEY_MESSAGE_SAME
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
            self::ATTRIBUTE_ID                    => self::ATTRIBUTE_NAME_ID,
            self::ATTRIBUTE_NAME                  => self::ATTRIBUTE_NAME_NAME,
            self::ATTRIBUTE_EMAIL                 => self::ATTRIBUTE_NAME_EMAIL,
            self::ATTRIBUTE_ROLE_ID               => self::ATTRIBUTE_NAME_ROLE_ID,
            self::ATTRIBUTE_PASSWORD              => self::ATTRIBUTE_NAME_PASSWORD,
            self::ATTRIBUTE_PASSWORD_CONFIRMATION => self::ATTRIBUTE_NAME_PASSWORD_CONFIRMATION,
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

    /**
     * check user authority from header
     *
     * @param array $targets authorities by function(API)
     * @return boolean
     */
    protected function checkRequestAuthority(array $targets)
    {
        return in_array($this->header(Config::get('myapp.headers.authority')), $targets, true);
    }
}
