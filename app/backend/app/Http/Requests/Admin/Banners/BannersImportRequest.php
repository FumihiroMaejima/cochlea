<?php

namespace App\Http\Requests\Admin\Banners;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
// use Illuminate\Http\UploadedFile;
// use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Config;
use App\Http\Requests\Admin\Banners\BannerBaseRequest;
use App\Library\Time\TimeLibrary;

class BannersImportRequest extends BannerBaseRequest
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
            'file' => 'file|max:1000|mimes:xlsx|mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
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
            'file'      => ':attributeはファイルをアップロードする必要があります。',
            'uploaded'  => ':attributeがアップロードされていません。',
            'max'       => ':attributeは:maxキロバイトまでアップロード出来ます。',
            'mimes'     => ':attributeの拡張子が正しくありません。',
            'mimetypes' => ':attributeは:valuesのファイル形式でアップロードしてください。'
        ];
    }
}
