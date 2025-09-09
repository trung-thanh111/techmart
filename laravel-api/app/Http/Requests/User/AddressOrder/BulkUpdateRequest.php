<?php

namespace App\Http\Requests\User\AddressOrder;

use App\Enums\Common;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Http\Requests\BaseRequest;


class BulkUpdateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }
    /**
     * rules slug
     * $rules["data.$key.slug"] = [
     *          "required",
     *           "string",
     *            Rule::unique("user_catalogues", "slug")->ignore($id),
     * ];
     *
     * @return array
     */
    public function rules(): array
    {
        $method = $this->getMethod();
        $sometime = ($method === 'PATCH') ? 'sometimes' : '';

        $rules = [];
        foreach ($this->input('data', []) as $key => $val) {
            $id = $val['id'] ?? null;
            $rules["data.$key.title"] = "nullable|string|max:255|{$sometime}";
            $rules["data.$key.slug"] = [
                "required",
                "string",
                "max:255",
                Rule::unique('address_orders', 'slug')->ignore($id),
                "{$sometime}"
            ];
            $rules["data.$key.full_name"] = "required|string|max:255|{$sometime}";
            $rules["data.$key.phone"] = "required|string|max:20|{$sometime}";
            $rules["data.$key.email"] = "nullable|email|max:255|{$sometime}";
            $rules["data.$key.address"] = "required|string|max:255|{$sometime}";
            $rules["data.$key.state"] = "nullable|string|max:100|{$sometime}";
            $rules["data.$key.postal_code"] = "required|string|max:20|{$sometime}";
            $rules["data.$key.publish"] = "required|integer|in:0,1,2|{$sometime}";
        }
        return $rules;
    }

    public function prepareForValidation()
    {
        $data = collect($this->input('data', []))->map(function ($item) {
            return array_merge($item, [
                'user_id' => auth(Common::API)->user()->id,
                'slug' => $item['slug'] ?? Str::slug($item['title'] ?? ''),
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
