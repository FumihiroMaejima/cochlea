<?php

namespace App\Http\Requests\Admin\Coins;

use Illuminate\Support\Facades\Config;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Http\Requests\BaseRequest;
use App\Http\Requests\Admin\Coins\CoinBaseRequest;
use App\Library\Time\TimeLibrary;

class CoinCreateRequest extends CoinBaseRequest
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
            'name'        => 'required|string|between:1,50',
            'detail'      => 'required|string|between:1,100',
            'price'       => 'required|integer|min:0|max:100000',
            'cost'        => 'required|integer|min:0|max:100000',
            'start_at'    => 'required|date|date_format:'.TimeLibrary::DEFAULT_DATE_TIME_FORMAT_SLASH,
            'end_at'      => 'required|date|date_format:'.TimeLibrary::DEFAULT_DATE_TIME_FORMAT_SLASH.'|after:start_at',
            'image'       => 'file|image|max:512', // 最大512KB

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
            'integer'     => ':attributeは整数で入力してください。',
            'string'      => ':attributeは文字列を入力してください。',
            'array'       => ':attributeは配列で入力してください。',
            'between'     => ':attributeは:min〜:max文字以内で入力してください。',
            'min'         => ':attributeは:min以上で入力してください。',
            'max'         => ':attributeは:max以下で入力してください。',
            'date'        => ':attributeは日付の形式で入力してください。',
            'date_format' => ':attributeは:date_formatの形式で入力してください。',
            'after'       => ':attributeは公開開始日時より後の日付で入力してください。',
            'file'        => ':attributeはファイル形式で入力してください。',
            'image'       => ':attributeは画像ファイルで入力してください。',
            'image.max'   => ':attributeは最大:max KBで入力してください。',
            'exists'      => '指定した:attributeは存在しません。'
        ];
    }
}
