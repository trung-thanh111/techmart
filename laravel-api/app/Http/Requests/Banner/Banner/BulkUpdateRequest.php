<?php

namespace App\Http\Requests\Banner\Banner;

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
            $rules["data.$key.name"] = "required|string|max:255|{$sometime}";
            $rules["data.$key.slug"] = "nullable|string|max:255|{$sometime}";
            $rules["data.$key.image"] = "nullable|string|max:255|{$sometime}";
            $rules["data.$key.type"] = "required|in:homepage,category,flash_sale,popup,custom|{$sometime}";
            $rules["data.$key.position"] = "required|string|max:100|{$sometime}";
            $rules["data.$key.device"] = "required|in:all,desktop,tablet,mobile|{$sometime}";
            $rules["data.$key.display_mode"] = "required|in:permanent,scheduled|{$sometime}";
            $rules["data.$key.start_at"] = "nullable|date|{$sometime}";
            $rules["data.$key.end_at"] = "nullable|date|after_or_equal:data.$key.start_at|{$sometime}";
            $rules["data.$key.sort_order"] = "nullable|integer|{$sometime}";
            $rules["data.$key.link_url"] = "nullable|string|max:255|{$sometime}";
            $rules["data.$key.width"] = "nullable|string|max:50|{$sometime}";
            $rules["data.$key.height"] = "nullable|string|max:50|{$sometime}";
            $rules["data.$key.alt"] = "nullable|string|max:255|{$sometime}";
            $rules["data.$key.class"] = "nullable|string|max:255|{$sometime}";
            $rules["data.$key.loading"] = "nullable|in:lazy,eager,auto|{$sometime}";
            $rules["data.$key.publish"] = "required|integer|in:0,1,2|{$sometime}";
        }
        return $rules;
    }

    public function prepareForValidation()
    {
        $data = collect($this->input('data', []))->map(function ($item) {
            return array_merge($item, [
                'user_id' => $item['user_id'] ?? auth(Common::API)->user()->id,
                'slug' => $item['slug'] ?? Str::slug($item['name'] ?? ''),
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
