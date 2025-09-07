<?php
namespace App\Http\Requests\Attribute\Attribute;

use App\Enums\Common;
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
            $rules["data.$key.publish"] = "required|integer|in:1,2|{$sometime}";
            $rules["data.$key.description"] = "nullable|string|max:255|{$sometime}";
            $rules["data.$key.attribute_catalogue_id"] = "required|integer|min:0|{$sometime}";
        }
        return $rules;
    }

    public function prepareForValidation()
    {
        $data = collect($this->input('data', []))->map(function ($item) {
            return array_merge($item, [
                'slug' => $item['slug'] ?? generate_slug($item['name'] ?? ''),
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
