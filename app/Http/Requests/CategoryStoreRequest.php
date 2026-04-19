<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'category_name' => 'required|string|unique:categories,category_name',
            'status' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'category_name.required' => 'The category name field is required.',
            'category_name.string' => 'The category name field must be string.',
            'category_name.unique' => 'The category name field must be unique.',
            'status.required' => 'The category name field is required.',
            'status.numeric' => 'The category name field must be numeric.',

        ];
    }
}
