<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Repositories\Admins\Roles\RolesRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use App\Models\Masters\Roles;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Config;

class BaseRequest extends FormRequest
{
    protected const AUTHORIZATION_ERROR_STATUS_CODE = 403;
    protected const AUTHORIZATION_ERROR_MESSAGE = 'Forbidden';
    protected const VALIDATION_ERROR_STATUS_CODE = 422;
    protected const VALIDATION_ERROR_MESSAGE= 'Unprocessable Entity';

    // error response key
    protected const ERROR_RESPONSE_KEY_STATUS = 'status';
    protected const ERROR_RESPONSE_KEY_ERRORS = 'errors';
    protected const ERROR_RESPONSE_KEY_MESSAGE = 'message';

    // attribute keys
    protected const ATTRIBUTE_ID                    = 'id';
    protected const ATTRIBUTE_NAME                  = 'name';
    protected const ATTRIBUTE_EMAIL                 = 'email';
    protected const ATTRIBUTE_ROLE_ID               = 'roleId';
    protected const ATTRIBUTE_PASSWORD              = 'password';
    protected const ATTRIBUTE_PASSWORD_CONFIRMATION = 'password_confirmation';
    protected const ATTRIBUTE_TEL                   = 'tel';
    protected const ATTRIBUTE_FILE                  = 'file';
    protected const ATTRIBUTE_IMAGE                 = 'image';

    // attribute name
    protected const ATTRIBUTE_NAME_ID                    = 'id';
    protected const ATTRIBUTE_NAME_NAME                  = '氏名';
    protected const ATTRIBUTE_NAME_EMAIL                 = 'メールアドレス';
    protected const ATTRIBUTE_NAME_ROLE_ID               = '権限';
    protected const ATTRIBUTE_NAME_PASSWORD              = 'パスワード';
    protected const ATTRIBUTE_NAME_PASSWORD_CONFIRMATION = '確認用パスワード';
    protected const ATTRIBUTE_NAME_TEL                   = '電話番号';
    protected const ATTRIBUTE_NAME_FILE                  = 'ファイル';
    protected const ATTRIBUTE_NAME_IMAGE                 = '画像';

    // rules
    protected const RULE_NAME = 'required|string|between:1,50';
    protected const RULE_EMAIL = 'required|string|email:rfc|between:1,50';
    protected const RULE_ROLE_ID = 'required|integer|exists:'; // model のidカラムを指定(. $roleModel->getTable() . ',id')
    protected const RULE_PASSWORD = 'required|string|between:8,100|confirmed';
    protected const RULE_PASSWORD_CONFIRMATION = 'same:password';
    protected const RULE_TEL = 'required|regex:/^[0-9]{2,4}-[0-9]{2,4}-[0-9]{3,4}$/';
    // 例: Excelファイル
    protected const RULE_FILE = 'file|max:1000|mimes:xlsx|mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    // 最大512KB,指定された拡張子(jpg,png),最小縦横100px 最大縦横600px
    protected const RULE_IMAGE = 'nullable|file|image|max:512|mimes:jpg,png|dimensions:min_width=100,min_height=100,max_width=600,max_height=600';

    // rule key
    protected const RULE_KEY_EMAIL_EMAIL = 'email.email';
    protected const RULE_KEY_REQUIRED = 'required';
    protected const RULE_KEY_STRING = 'string';
    protected const RULE_KEY_BETWEEN = 'between';
    protected const RULE_KEY_CONFIRMED = 'confirmed';
    protected const RULE_KEY_SAME = 'same';
    protected const RULE_KEY_EMAIL = 'email';
    protected const RULE_KEY_TEL_REGEX = 'tel.regex';
    protected const RULE_KEY_FILE = 'file';
    protected const RULE_KEY_FILE_MAX = 'file.max';
    protected const RULE_KEY_FILE_MIMES = 'file.mimes';
    protected const RULE_KEY_FILE_MIME_TYPES = 'file.mimetypes';
    protected const RULE_KEY_IMAGE = 'image';
    protected const RULE_KEY_IMAGE_MAX = 'image.max';
    protected const RULE_KEY_IMAGE_MIMES = 'image.mimes';
    protected const RULE_KEY_IMAGE_MIME_TYPES = 'image.mimetypes';
    protected const RULE_KEY_IMAGE_DIMENTIONS = 'image.dimensions';

    // message
    protected const RULE_KEY_MESSAGE_EMAIL_EMAIL = ':attributeの形式が正しくありません。';
    protected const RULE_KEY_MESSAGE_REQUIRED = ':attributeは必須項目です。';
    protected const RULE_KEY_MESSAGE_STRING = ':attributeは文字列を入力してください。';
    protected const RULE_KEY_MESSAGE_BETWEEN = ':attributeは:min〜:max文字以内で入力してください。';
    protected const RULE_KEY_MESSAGE_CONFIRMED = ':attributeは確認用にもう一度入力してください。';
    protected const RULE_KEY_MESSAGE_SAME = ':attributeは同一の値ではありません。';
    protected const RULE_KEY_MESSAGE_EMAIL = 'アルファベット半角で入力してください。';
    protected const RULE_KEY_MESSAGE_TEL_REGEX = '「000-0000-0000」の形式で入力してください。';
    protected const RULE_KEY_MESSAGE_FILE = ':attributeはファイルをアップロードしてください。';
    protected const RULE_KEY_MESSAGE_FILE_MAX = 'ファイルの容量を超過しています。';
    protected const RULE_KEY_MESSAGE_FILE_MIMES = '指定された拡張子のファイルをアップロードして下さい。';
    protected const RULE_KEY_MESSAGE_FILE_MIME_TYPES = '許可されていない形式のファイルがアップロードされました。';
    protected const RULE_KEY_MESSAGE_IMAGE = ':attributeは画像ファイルをアップロードしてください。';
    protected const RULE_KEY_MESSAGE_IMAGE_MAX = 'ファイルの容量を超過しています。';
    protected const RULE_KEY_MESSAGE_IMAGE_MIMES = '指定された拡張子のファイルをアップロードして下さい。';
    protected const RULE_KEY_MESSAGE_IMAGE_MIME_TYPES = '許可されていない形式のファイルがアップロードされました。';
    protected const RULE_KEY_MESSAGE_IMAGE_DIMENTIONS = ':attributeは指定された縦横のサイズの範囲でアップロードしてください。';

    // authority
    protected const NO_AUTHORITIES_COUNT = 0;

    /** @var array<int, string>|null $requestAuthorities approved autorities in this requst */
    protected array|null $requestAuthorities = null;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (is_null($this->requestAuthorities)) {
            return true;
        } else {
            return $this->checkRequestAuthority($this->requestAuthorities);
        }
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
            self::ATTRIBUTE_NAME                  => self::RULE_NAME,
            self::ATTRIBUTE_EMAIL                 => self::RULE_EMAIL,
            // 'email' => ['regex:/^.+@.+$/i']
            self::ATTRIBUTE_ROLE_ID               => self::RULE_ROLE_ID . $roleModel->getTable() . ',id',
            self::ATTRIBUTE_PASSWORD              => self::RULE_PASSWORD,
            self::ATTRIBUTE_PASSWORD_CONFIRMATION => self::RULE_PASSWORD_CONFIRMATION,
            self::ATTRIBUTE_TEL                   => self::RULE_TEL,
            self::ATTRIBUTE_FILE                  => self::RULE_FILE,
            self::ATTRIBUTE_IMAGE                 => self::RULE_IMAGE,
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
            self::RULE_KEY_EMAIL_EMAIL      => self::RULE_KEY_MESSAGE_EMAIL_EMAIL,
            self::RULE_KEY_REQUIRED         => self::RULE_KEY_MESSAGE_REQUIRED,
            self::RULE_KEY_STRING           => self::RULE_KEY_MESSAGE_STRING,
            self::RULE_KEY_BETWEEN          => self::RULE_KEY_MESSAGE_BETWEEN,
            self::RULE_KEY_CONFIRMED        => self::RULE_KEY_MESSAGE_CONFIRMED,
            self::RULE_KEY_SAME             => self::RULE_KEY_MESSAGE_SAME,
            self::RULE_KEY_TEL_REGEX        => self::RULE_KEY_MESSAGE_TEL_REGEX,
            self::RULE_KEY_FILE             => self::RULE_KEY_MESSAGE_FILE,
            self::RULE_KEY_FILE_MAX         => self::RULE_KEY_MESSAGE_FILE_MAX,
            self::RULE_KEY_FILE_MIMES       => self::RULE_KEY_MESSAGE_FILE_MIMES,
            self::RULE_KEY_FILE_MIME_TYPES  => self::RULE_KEY_MESSAGE_FILE_MIME_TYPES,
            self::RULE_KEY_IMAGE            => self::RULE_KEY_MESSAGE_IMAGE,
            self::RULE_KEY_IMAGE_MAX        => self::RULE_KEY_MESSAGE_IMAGE_MAX,
            self::RULE_KEY_IMAGE_MIMES      => self::RULE_KEY_MESSAGE_IMAGE_MIMES,
            self::RULE_KEY_IMAGE_MIME_TYPES => self::RULE_KEY_MESSAGE_IMAGE_MIME_TYPES,
            self::RULE_KEY_IMAGE_DIMENTIONS => self::RULE_KEY_MESSAGE_IMAGE_DIMENTIONS,
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
            self::ATTRIBUTE_TEL                   => self::ATTRIBUTE_NAME_TEL,
            self::ATTRIBUTE_FILE                  => self::ATTRIBUTE_NAME_FILE,
            self::ATTRIBUTE_IMAGE                 => self::ATTRIBUTE_NAME_IMAGE,
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
