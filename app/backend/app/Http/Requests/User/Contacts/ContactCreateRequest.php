<?php

namespace App\Http\Requests\User\Contacts;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Rule;
use App\Http\Requests\User\Contacts\ContactBaseRequest;
use App\Library\Time\TimeLibrary;
use App\Models\Masters\Contacts;

class ContactCreateRequest extends ContactBaseRequest
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
        return [
            'name'           => 'string|between:1,50',
            'email'          => 'required|string|email:rfc|between:1,50',
            'type'           => 'required|integer|' . Rule::in(Contacts::CONTACT_CATEGORIES),
            'detail'         => 'string|max:1000',
            'failure_detail' => 'string|max:1000',
            'failure_at'     => 'date|date_format:'.TimeLibrary::DEFAULT_DATE_TIME_FORMAT_SLASH,
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
            'integer'     => ':attributeは整数で入力してください。',
            'string'      => ':attributeは文字列を入力してください。',
            'between'     => ':attributeは:min〜:max文字以内で入力してください。',
            'confirmed'     => ':attributeは確認用にもう一度入力してください。',
            'same'     => ':attributeは同一の値ではありません。',
            'max'         => ':attributeは:max以下で入力してください。',
            'date'        => ':attributeは日付の形式で入力してください。',
            'date_format' => ':attributeは:date_formatの形式で入力してください。',
            // 'email' => 'アルファベット半角で入力してください。'
            // 'tel.regex' => '「000-0000-0000」の形式で入力してください。'
        ];
    }
}
