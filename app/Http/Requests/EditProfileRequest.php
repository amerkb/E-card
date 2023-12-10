<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditProfileRequest extends FormRequest
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
            'firstName' => 'string|max:255',
            'lastName' => 'string|max:255',
            'jobTitle' => 'nullable|string|max:255',
            'businessName' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'cover' => 'nullable|image|mimes:jpeg,jpg,png',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png',
            'theme_id' => 'nullable|exists:themes,id',
            'bgColor' => 'nullable|string|max:255',
            'buttonColor' => 'nullable|string|max:255',
            'phoneNum' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'primaryLinks.*.id' => 'sometimes|exists:primary_links,id',
            'primaryLinks.*.value' => 'sometimes|string|max:255',
            'secondLinks.*.id' => 'sometimes|exists:links,id',
            'secondLinks.*.name_link' => 'sometimes|string|max:255',
            'secondLinks.*.link' => 'sometimes|string|max:255',
            'secondLinks.*.logo' => 'sometimes|image|max:2048|mimes:jpeg,jpg,png',
            'sections.*.id' => 'sometimes|exists:sections,id',
            'sections.*.title' => 'sometimes|string|max:255',
            'sections.*.name_of_file' => 'sometimes|string',
            'sections.*.media' => 'sometimes|file|mimes:jpeg,jpg,png,pdf,mp4'
        ];
    }
}
