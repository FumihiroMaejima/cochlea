<?php

namespace App\Http\Requests\Admin\Informations;

use App\Http\Requests\Admin\Informations\InformationBaseRequest;
use App\Models\Masters\Informations;

class InformationDeleteRequest extends InformationBaseRequest
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
        $model = new Informations();
        return [
            'informations' => 'required|array|exists:' . $model->getTable() . ',id'
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
            'required' => ':attributeは必須項目です。',
            'array'    => ':attributeは配列で入力してください。',
            'exists'   => '指定した:attributeは存在しません。'
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
            self::KEY_INFORMATIONS => 'お知らせ情報'
        ];
    }
}
