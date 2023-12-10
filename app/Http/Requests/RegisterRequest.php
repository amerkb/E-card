<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'userName' => 'required|string',
            'password' => 'required|min:6|max:24|string',
            'uuid' => 'required|string|unique:users,uuid'
        ];
    }
    public function messages(): array
{
   return [
       'email.required' => 'The email field is required.',
       'email.email' => 'The email must be a valid email address.',
       'email.unique' => 'The email has already been taken.',
       'password.required' => 'The password field is required.',
       'password.min' => 'The password must be at least 6 characters.',
       'password.max' => 'The password may not be greater than 24 characters.',
       'uuid.required' => 'The uuid field is required.',
       'uuid.unique' => 'The uuid has already been taken.',
   ];
}
}
