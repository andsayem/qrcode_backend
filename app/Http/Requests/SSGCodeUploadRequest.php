<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SSGCodeUploadRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'csv_file' => 'required|mimes:csv,txt|max:20480'
        ];
    }

    public function messages()
    {
        return [
            'csv_file.required' => 'The csv file field is required.',
            'csv_file.mimes' => 'File format must be csv.',
            'csv_file.max' => 'File max size 20MB',
        ];
    }
}
