<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Admins;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Collection;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Http\Requests\Admin\Admins\AdminBaseRequest;
use App\Http\Requests\BaseRequest;
use App\Models\Masters\Admins;

class AdminDeleteRequest extends AdminBaseRequest
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
        $adminsModel = app()->make(Admins::class);
        return [
            'id' => 'required|integer|exists:' . $adminsModel->getTable() . ',id'
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
            'integer'  => ':attributeは整数を入力してください。',
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
            self::KEY_ID => '管理者ID'
        ];
    }
}
