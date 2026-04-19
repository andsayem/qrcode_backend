<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOfferRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active' => 'required',
            'point_value' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'The title field is required.',
            'title.string' => 'The title field must be string.',
            'file.required' => 'The file field must be image.',

            'point_value.required' => 'The point_value field is required.',
            'is_active.required' => 'The status field must be numeric.',
        ];
    }
}