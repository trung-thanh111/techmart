<?php

namespace App\Http\Requests\Banner\Banner;

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
            'banners.*.name' => 'required|string|max:255',
            'banners.*.slug' => 'nullable|string|max:255|unique:banners,slug',
            'banners.*.image' => 'nullable|string|max:255',
            'banners.*.type' => 'required|in:homepage,category,flash_sale,popup,custom',
            'banners.*.position' => 'required|string|max:100',
            'banners.*.device' => 'required|in:all,desktop,tablet,mobile',
            'banners.*.display_mode' => 'required|in:permanent,scheduled',
            'banners.*.start_at' => 'nullable|date',
            'banners.*.end_at' => 'nullable|date|after_or_equal:banners.*.start_at',
            'banners.*.sort_order' => 'nullable|integer',
            'banners.*.link_url' => 'nullable|string|max:255',
            'banners.*.width' => 'nullable|string|max:50',
            'banners.*.height' => 'nullable|string|max:50',
            'banners.*.alt' => 'nullable|string|max:255',
            'banners.*.class' => 'nullable|string|max:255',
            'banners.*.loading' => 'nullable|in:lazy,eager,auto',
            'banners.*.publish' => 'required|integer|in:0,1,2',
        ];
    }

    public function prepareForValidation()
    {
        $data = collect($this->input('data', []))->map(function ($item) {
            unset($item['id']);
            return array_merge($item, [
                'user_id' => $item['user_id'] ?? auth(Common::API)->user()->id,
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
