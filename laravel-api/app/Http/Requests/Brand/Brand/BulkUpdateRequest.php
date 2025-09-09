<?php

namespace App\Http\Requests\Brand\Brand;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Enums\Common;

class BulkUpdateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }
    /**
     * rules slug
     * $rules["data.$key.slug"] = [
     *          'required',
     *           'string',
     *            Rule::unique('user_catalogues', 'slug')->ignore($id),
     * ];
     *
     * @return array
     */
    public function rules(): array
    {
        $method = $this->getMethod();
        $sometime = ($method === 'PATCH') ? 'sometimes' : '';

        $rules = [
            'data' => 'required|array|min:1',
            'data.*.id' => 'required|integer|exists:brands,id',
        ];

        foreach ($this->input('data', []) as $key => $val) {
            $id = $val['id'] ?? null;
            $rules["data.$key.name"] = "required|string|max:255|{$sometime}";
            $rules["data.$key.slug"] = [
                "required",
                "string",
                "max:255",
                Rule::unique('brands', 'slug')->ignore($id),
                "{$sometime}"
            ];
            $rules["data.$key.description"] = "nullable|string|{$sometime}";
            $rules["data.$key.logo"] = "nullable|string|max:500|{$sometime}";
            $rules["data.$key.website"] = "nullable|url|max:255|{$sometime}";
            $rules["data.$key.country"] = "nullable|string|max:100|{$sometime}";
            $rules["data.$key.order"] = "nullable|integer|min:0|{$sometime}";
            $rules["data.$key.publish"] = "required|integer|in:0,1,2|{$sometime}";
            $rules["data.$key.user_id"] = "required|integer|exists:users,id|{$sometime}";
        }

        return $rules;
    }

    public function prepareForValidation()
    {
        $data = collect($this->input('data', []))->map(function ($item) {
            return array_merge($item, [
                'slug' => $item['slug'] ?? Str::slug($item['name'] ?? ''),
                'user_id' => $item['user_id'] ?? auth(Common::API)->user()->id,
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
