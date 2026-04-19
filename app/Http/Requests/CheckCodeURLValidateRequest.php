<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckCodeURLValidateRequest extends FormRequest
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
            'mobile' => 'required|min:11|regex:/^(?:\+?88)?01[3-9]\d{8}$/|numeric' ,
            'code' => 'required|string|min:14',
        ];
    }

    public function messages()
    {
        return [
            'mobile.required' => 'The mobile field is required.',
            'mobile.min' => 'The mobile must be min 11 character long.',
            'mobile.max' => 'The mobile must be max 13 character long.',
            'mobile.regex' => 'The mobile number is invalid.',
            'code.required' => 'The code field is required.',
            'code.string' => 'The code field is string.',
            'code.min' => 'The code must be min 14 character long.',
            'code.exists' => 'The code is invalid.',
        ];
    }
}
