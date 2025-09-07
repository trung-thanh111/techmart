<?php
namespace App\Http\Requests\Product\ProductCatalogue;

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
            'data' => 'required|array|min:1',
            'data.*.parent_id' => 'nullable|integer|exists:product_catalogues,id',
            'data.*.lft' => 'nullable|integer',
            'data.*.rgt' => 'nullable|integer',
            'data.*.level' => 'nullable|integer|min:0',
            'data.*.name' => 'required|string|max:255',
            'data.*.description' => 'nullable|string',
            'data.*.image' => 'nullable|string|max:255',
            'data.*.icon' => 'nullable|string|max:255',
            'data.*.order' => 'nullable|integer|min:0',
            'data.*.publish' => 'required|integer|in:1,2',
            'data.*.meta_title' => 'nullable|string|max:255',
            'data.*.meta_keyword' => 'nullable|string|max:255',
            'data.*.meta_description' => 'nullable|string|max:500',
            'data.*.index' => 'nullable|integer|in:1,2',
            'data.*.scheme' => 'nullable|string',
            'data.*.iframe' => 'nullable|string',
        ];
    }

    public function prepareForValidation()
    {
        $data = collect($this->input('data', []))->map(function ($item) {
            unset($item['id']);
            return array_merge($item, [
                'slug' => $item['slug'] ?? Str::slug($item['name'] ?? ''),
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
