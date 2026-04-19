<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if ($this->id) {
            return [
                'name' => 'required|max:255',
                'email' => 'required|unique:users,email,' . $this->id,
                'password' => 'nullable|min:8|same:confirm_password',
                'roles' => 'required'
            ];
        }

        return [
            'name' => 'required|max:255',
            'email' => 'required|unique:users,email',
            'password' => 'required|min:8|same:confirm_password',
            'roles' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.unique' => 'The email must be unique.',
            'email.email' => 'The email is invalid.',
            'password.required' => 'The password field is required.',
            'password.same' => 'The password & confirmation message must match.',
            'password.min' => 'The password is minimum 8 character.',
            'email.roles' => 'The roles field is required.',
        ];
    }
}
