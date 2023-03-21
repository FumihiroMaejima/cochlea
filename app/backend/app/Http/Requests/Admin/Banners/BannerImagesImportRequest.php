<?php

namespace App\Http\Requests\Admin\Banners;

use App\Http\Requests\Admin\Banners\BannerBaseRequest;

class BannerImagesImportRequest extends BannerBaseRequest
{
    // 最大512KB,指定された拡張子(jpg,png),最小縦横100px 最大縦横600px
    protected const RULE_BANNER_IMAGE = 'file|image|max:512|mimes:jpg,png|dimensions:min_width=100,min_height=100,max_width=600,max_height=600';

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
            // 'image' => 'file|image|max:512|mimes:png|mimetypes:application/png', // 最大512KB
            'image' => self::RULE_BANNER_IMAGE,
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
            'min'         => ':attributeは:min以上で入力してください。',
            'max'         => ':attributeは:max以下で入力してください。',
            'file'        => ':attributeはファイル形式で入力してください。',
            'image'       => ':attributeは画像ファイルで入力してください。',
            'image.max'   => ':attributeは最大:max KBで入力してください。',
            'exists'      => '指定した:attributeは存在しません。',
            'mimes'       => ':attributeの拡張子が正しくありません。',
            'mimetypes'   => ':attributeは:valuesのファイル形式でアップロードしてください。'
        ];
    }
}
