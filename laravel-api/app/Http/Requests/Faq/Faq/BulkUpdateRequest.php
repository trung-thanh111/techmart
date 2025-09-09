<?php

namespace App\Http\Requests\Faq\Faq;

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
            $rules["data.$key.question"] = "required|string|max:255|{$sometime}";
            $rules["data.$key.answer"] = "required|string|{$sometime}";
            $rules["data.$key.sort_order"] = "nullable|integer|min:0|{$sometime}";
            $rules["data.$key.is_featured"] = "nullable|boolean|{$sometime}";
            $rules["data.$key.faq_catalogue_id"] = "required|integer|exists:faq_catalogues,id|{$sometime}";
            $rules["data.$key.publish"] = "required|integer|in:0,1,2|{$sometime}";
        }
        return $rules;
    }

    public function prepareForValidation()
    {
        $data = collect($this->input('data', []))->map(function ($item) {
            return array_merge($item, [
                'slug' => $item['slug'] ?? Str::slug($item['question'] ?? ''),
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
