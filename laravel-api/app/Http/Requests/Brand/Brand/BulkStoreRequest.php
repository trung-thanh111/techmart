<?php

namespace App\Http\Requests\Brand\Brand;

use App\Enums\Common;
use App\Http\Requests\BaseRequest;
use Illuminate\Support\Str;

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
            "data" => "required|array|min:1",
            "data.*.name" => "required|string|max:255",
            "data.*.description" => "nullable|string",
            "data.*.logo" => "nullable|string|max:500",
            "data.*.website" => "nullable|url|max:255",
            "data.*.country" => "nullable|string|max:100",
            "data.*.order" => "nullable|integer|min:0",
            "data.*.publish" => "required|integer|in:0,1,2",
        ];
    }

    public function prepareForValidation()
    {
        $data = collect($this->input('data', []))->map(function ($item) {
            unset($item['id']);
            return array_merge($item, [
                'slug' => $item['slug'] ?? Str::slug($item['name'] ?? ''),
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
