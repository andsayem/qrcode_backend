<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestCodeApprovalRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => 'required|numeric|exists:request_codes,id',
            'status' => 'required|numeric',
            //'comments' => 'required_if:status,4',
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'The id field is required.',
            'id.numeric' => 'The id field must be numeric.',
            'id.exists' => 'The id must exist in the system.',
            'status.required' => 'The status field is required.',
            'status.numeric' => 'The status field must be numeric.',
        ];
    }
}
