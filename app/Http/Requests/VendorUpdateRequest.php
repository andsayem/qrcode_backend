<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendorUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'vendor_name' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'vendor_name.required' => 'The vendor name field is required.',
            'vendor_name.string' => 'The vendor name field must be string.',
        ];
    }
}
