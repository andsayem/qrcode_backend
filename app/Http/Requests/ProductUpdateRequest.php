<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'product_name' => 'required|string',
            'sku' => 'required|string|max:2|unique:products,sku,' . $this->product,
            'category_id' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'product_name.required' => 'The product name field is required.',
            'product_name.string' => 'The product name field must be string.',
            'sku.required' => 'The sku field is required.',
            'sku.string' => 'The sku field must be string.',
            'sku.unique' => 'The sku field must be unique.',
            'sku.max' => 'The sku field max length 2.',
            'category_id.required' => 'The category field is required.',
            'category_id.numeric' => 'The category field must be numeric.',
        ];
    }
}
