<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExampleBulkStoreRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:example_models,email',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive,pending'
        ];
    }
    
    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Tên là bắt buộc',
            'email.required' => 'Email là bắt buộc',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email đã tồn tại',
            'phone.required' => 'Số điện thoại là bắt buộc',
            'status.required' => 'Trạng thái là bắt buộc',
            'status.in' => 'Trạng thái phải là active, inactive hoặc pending'
        ];
    }
    
    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'Tên',
            'email' => 'Email',
            'phone' => 'Số điện thoại',
            'address' => 'Địa chỉ',
            'status' => 'Trạng thái'
        ];
    }
}
