<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileRequest extends FormRequest
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
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
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
            'secondLinks.*.name_link' => 'sometimes|string|max:255',
            'secondLinks.*.link' => 'sometimes|string|max:255',
            'secondLinks.*.logo' => 'sometimes|image|max:2048|mimes:jpeg,jpg,png',
            'sections.*.title' => 'sometimes|string|max:255',
            'sections.*.name_of_file' => 'sometimes|string',
            'sections.*.media' => 'sometimes|file|mimes:jpeg,jpg,png,pdf,mp4'
        ];
    }

}
