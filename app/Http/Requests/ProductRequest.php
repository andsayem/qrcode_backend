<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'product_name' => 'required|string',
            'sku' => 'required|string|max:2|regex:/^[A-Z]+$/|unique:products,sku,'.$this->product,
            'point_slab' => 'required|numeric',
            'category_id' => 'required|numeric',
            'channel_id' => 'required|numeric',
            'status' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'product_name.required' => 'The product name field is required.',
            'product_name.string' => 'The product name field must be string.',
            'sku.required' => 'The sku field is required.',
            'sku.string' => 'The sku field must be string.',
            'sku.regex' => 'The sku field must be uppercase.',
            'sku.unique' => 'The sku field must be unique.',
            'point_slab.required' => 'The point slab field is required.',
            'point_slab.numeric' => 'The point slab field must be numeric.',
            'category_id.required' => 'The category field is required.',
            'category_id.numeric' => 'The category field must be numeric.',
            'channel_id.required' => 'The channel field is required.',
            'channel_id.numeric' => 'The channel field must be numeric.',
            'status.required' => 'The category name field is required.',
            'status.numeric' => 'The category name field must be numeric.',
            'sku.max' => 'The sku field max length 2.',
        ];
    }
}
