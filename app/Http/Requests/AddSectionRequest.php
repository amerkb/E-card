<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddSectionRequest extends FormRequest
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
            'title' => 'sometimes|string|max:255',
            'name_of_file' => 'sometimes|string',
            'media' => 'sometimes|file|image|mimes:png,jpg,pdf,mp4'
        ];
    }
}
