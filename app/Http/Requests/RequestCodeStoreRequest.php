<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestCodeStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'product_id' => 'required|numeric',
            'code_length' => 'required|numeric',
            'total_no_of_code' => 'required|numeric|max:5000000', 
        ];
    }

    public function messages()
    {
        return [
            'product_id.required' => 'The product sku field is required.',
            'product_id.numeric' => 'The product sku field must be numeric.',
            'code_length.required' => 'The code length field is required.',
            'code_length.numeric' => 'The code length field must be numeric.',
            'total_no_of_code.required' => 'The total no of code field is required.',
            'total_no_of_code.numeric' => 'The total no of code field must be numeric.',
            'total_no_of_code.max' => 'The total no of code field max 500000.',
            'vendor_id.required' => 'The vendor field is required.', 
        ];
    }
}
