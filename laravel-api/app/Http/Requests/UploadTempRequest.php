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
        $maxSize = config('upload.image.max_size');
        $mimes   = 'jpeg,jpg,png,gif,webp';
        return [
            'file'   => 'required',
            'file.*' => "mimes:{$mimes}|max:{$maxSize}",
            'session_id' => 'required|string|max:255',
        ];
    }

    public function prepareForValidation() {}

    public function messages()
    {
        return [];
    }
}
