<?php

namespace App\Http\Requests\Admin\Debug;

use Illuminate\Support\Facades\Config;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Http\Requests\BaseRequest;
use App\Library\Time\TimeLibrary;

class DebugFileUploadRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->requestAuthorities = Config::get('myapp.executionRole.services.debug');
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
        // $this->merge(['id' => $this->route('id')]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            self::ATTRIBUTE_NAME_IMAGE       => self::RULE_IMAGE,

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
            'integer'     => ':attributeは整数で入力してください。',
            'file'        => ':attributeはファイル形式で入力してください。',
            'image'       => ':attributeは画像ファイルで入力してください。',
            'image.max'   => ':attributeは最大:max KBで入力してください。',
            'image.mimes' => self::RULE_KEY_MESSAGE_IMAGE_MIMES,
            'image.dimentions' => self::RULE_KEY_MESSAGE_IMAGE_DIMENTIONS,
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
            'image'    => 'デバッグ画像イメージ',
        ];
    }
}
