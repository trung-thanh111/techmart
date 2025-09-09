<?php

namespace App\Http\Requests\User\AddressOrder;

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
            "data.*.title" => "nullable|string|max:255",
            "data.*.slug" => "required|string|max:255|unique:address_orders,slug",
            "data.*.full_name" => "required|string|max:255",
            "data.*.phone" => "required|string|max:20",
            "data.*.email" => "nullable|email|max:255",
            "data.*.address" => "required|string|max:255",
            "data.*.state" => "nullable|string|max:100",
            "data.*.postal_code" => "required|string|max:20",
            "data.*.country_code" => "nullable|string|max:2",
            "data.*.is_default" => "boolean",
            "data.*.publish" => "required|integer|in:0,1,2",
        ];
    }

    public function prepareForValidation()
    {
        $data = collect($this->input('data', []))->map(function ($item) {
            unset($item['id']);
            return array_merge($item, [
                'user_id' => auth(Common::API)->user()->id,
                'slug' => $item['slug'] ?? Str::slug($item['title'] ?? ''),
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
