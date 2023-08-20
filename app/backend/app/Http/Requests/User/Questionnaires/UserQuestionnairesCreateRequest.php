<?php

namespace App\Http\Requests\User\Questionnaires;

use App\Http\Requests\User\Questionnaires\QuestionnairesBaseRequest;

class UserQuestionnairesCreateRequest extends QuestionnairesBaseRequest
{
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
        return [
            'id' => 'required|integer',
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
        ];
    }
}
