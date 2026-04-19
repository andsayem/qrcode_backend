<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'category_name' => 'required|string|unique:categories,category_name,' . $this->category,
            'status' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'category_name.required' => 'The category name field is required.',
            'category_name.unique' => 'The category name field must be unique.',
            'category_name.string' => 'The category name field must be string.',

            'status.required' => 'The status field is required.',
            'status.numeric' => 'The status field must be numeric.',
        ];
    }
}
