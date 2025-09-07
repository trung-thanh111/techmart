<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadTempRequest extends FormRequest
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
        $mimeTypes = implode(',', config('upload.image.allowed_type'));
        $maxSize = config('upload.image.max_size');
        return [
            "file" => "required|mimes:{$mimeTypes}|max:{$maxSize}",
            "session_id" => "required|string|max:255"
        ];
    }

    public function prepareForValidation() {}

    public function messages()
    {
        return [];
    }
}
