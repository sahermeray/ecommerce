<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LanguageRequest extends FormRequest
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
            'name'=>'required|max:100',
            'abbr'=>'required|max:10|string',
            'direction'=>'required|in:rtl,ltr'
        ];
    }

    public function messages(){
        return [
            'required'=>'هذا الحقل مطلوب',
            'name.string'=>'اسم اللغة لابد ان يكون احرف',
            'name.max'=>'اسم اللغة يجب ان لا يتجاوز 100 حرف',
            'in'=>'القيمة المدخلة غير صحيحة',
            'abbr.string'=>'اسم اللغة لابد ان يكون احرف',
            'abbr.max'=>'اسم اللغة يجب ان لا يتجاوز 100 حرف'

        ];
    }
}
