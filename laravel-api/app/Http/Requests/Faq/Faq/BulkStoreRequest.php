<?php

namespace App\Http\Requests\Faq\Faq;

use App\Enums\Common;
use Illuminate\Support\Str;
use App\Http\Requests\BaseRequest;

class BulkStoreRequest extends BaseRequest
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
            "data.*.question" => "required|string|max:255",
            "data.*.answer" => "required|string",
            "data.*.sort_order" => "nullable|integer|min:0",
            "data.*.is_featured" => "nullable|boolean",
            "data.*.faq_catalogue_id" => "required|integer|exists:faq_catalogues,id",
            "data.*.publish" => "required|integer|in:1,2",
        ];
    }

    public function prepareForValidation()
    {
        $data = collect($this->input('data', []))->map(function ($item) {
            unset($item['id']);
            return array_merge($item, [
                'slug' => $item['slug'] ?? Str::slug($item['question'] ?? ''),
                'user_id' => $item['user_id'] ?? auth(Common::API)->user()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        })->toArray();
        $this->merge(['data' => $data]);
    }

    public function messages()
    {
        return [];
    }
}
