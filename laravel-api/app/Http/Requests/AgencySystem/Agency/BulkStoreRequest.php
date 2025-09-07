<?php
namespace App\Http\Requests\AgencySystem\Agency;

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
            'data' => 'required|array|min:1',
            'data.*.name' => 'required|string|max:255',
            'data.*.description' => 'nullable|string',
            'data.*.logo' => 'nullable|max:255',
            'data.*.website' => 'nullable|string|max:255',
            'data.*.email' => 'nullable|string|email',
            'data.*.phone' => 'nullable|string|max:20',
            'data.*.address' => 'nullable|string|max:255',
            'data.*.iframe_map' => 'nullable|string',
            'data.*.publish' => 'required|integer|in:0,1,2',
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
