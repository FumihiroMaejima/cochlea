<?php

namespace App\Http\Requests\Admins;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Collection;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Repositories\Admins\Roles\RolesRepositoryInterface;
use App\Http\Requests\BaseRequest;
use App\Models\Roles;
// use Symfony\Component\HttpKernel\Exception\HttpException;
// use Illuminate\Validation\ValidationException;

class AdminUpdateRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->requestAuthorities = Config::get('myapp.executionRole.services.admins');
        return $this->checkRequestAuthority($this->requestAuthorities);
    }

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
        // ロールリストのidのみの配列を取得
        /* $rolesCollection = app()->make(RolesRepositoryInterface::class)->getRolesList();
        Log::info(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'collection pluck: ' . json_encode($rolesCollection->pluck('id'))); */
        $roleModel = app()->make(Roles::class);

        return [
            'id'     => 'required|integer',
            'name'   => 'required|string|between:1,50',
            'email'  => 'required|string|email:rfc|between:1,50',
            'roleId' => 'required|integer|exists:' . $roleModel->getTable() . ',id',
            // 'tel' => 'required|numeric|digits_between:8,11'
            // 'tel' => 'required|regex:/^[0-9]{2,4}-[0-9]{2,4}-[0-9]{3,4}$/'
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
            'id.integer'  => ':attributeは整数で入力してください。',
            'email.email' => ':attributeの形式が正しくありません。',
            'required'    => ':attributeは必須項目です。',
            'string'      => ':attributeは文字列を入力してください。',
            'between'     => ':attributeは:min〜:max文字以内で入力してください。'
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
            'id'     => 'id',
            'name'   => '氏名',
            'email'  => 'メールアドレス',
            'roleId' => '権限'
        ];
    }
}
