<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class VendorRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'vendor_name' => 'required|string',
            'contact_person' => 'required|string',
            'mobile' => 'required|min:11|regex:/^(?:\+?88)?01[3-9]\d{8}$/|numeric',
        ];
    }

    public function messages()
    {
        return [
            'vendor_name.required' => 'The vendor name field is required.',
            'vendor_name.string' => 'The vendor name field must be string.',
            'contact_person.required' => 'The contact person field is required.',
            'contact_person.string' => 'The contact person field must be string.',
            'mobile.required' => 'The mobile field is required.',
            'mobile.min' => 'The mobile must be min 11 character long.',
            'mobile.max' => 'The mobile must be max 13 character long.',
            'mobile.regex' => 'The mobile number is invalid.',
        ];
    }
}
