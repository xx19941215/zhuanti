<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShopPost extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:255',
            'url'  => 'required|max:255',
            'goods' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '缺少name字段',
            'url.required'  => '缺少url字段',
            'goods.required'  => '缺少goods字段',
        ];
    }
}
