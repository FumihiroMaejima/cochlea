<?php

namespace App\Http\Requests\User\Questionnaires;

use App\Models\Masters\Questionnaires;
use App\Http\Requests\User\Questionnaires\QuestionnairesBaseRequest;

class UserQuestionnairesUpdateRequest extends QuestionnairesBaseRequest
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
            'questions' => 'required|array',
            'questions.*.key' => 'required|int|min:1',
            'questions.*.text' => 'required|string',
            'questions.*.type' => 'required|int|min:1',
            'questions.*.defaultText' => 'string',
            'questions.*.chocies' => 'required|array',
            'questions.*.chocies.*.key' => 'required|int|min:1',
            'questions.*.chocies.*.name' => 'required|string',

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
            self::KEY_ID                 => 'アンケートID',
            self::KEY_QUESTIONS          => '解答情報',
            'questions.*.key'            => '質問情報キー',
            'questions.*.text'           => '質問名',
            'questions.*.type'           => '質問タイプ',
            'questions.*.defaultText'    => '質問デフォルトテキスト',
            'questions.*.chocies'        => '質問選択肢',
            'questions.*.chocies.*.key'  => '質問選択肢キー',
            'questions.*.chocies.*.name' => '質問選択肢名',
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
            'array'    => ':attributeは配列で入力してください。',
        ];
    }
}
