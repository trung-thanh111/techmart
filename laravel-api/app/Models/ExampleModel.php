<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Excelable;

class ExampleModel extends Model
{
    use HasFactory, Excelable;
    
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'status',
        'created_at',
        'updated_at'
    ];
    
    // Excel configuration
    protected static $excelFields = [
        'id',
        'name',
        'email',
        'phone',
        'address',
        'status',
        'created_at'
    ];
    
    protected static $excelImportFields = [
        'name',
        'email',
        'phone',
        'address',
        'status'
    ];
    
    protected static $excelFieldLabels = [
        'id' => 'ID',
        'name' => 'Tên',
        'email' => 'Email',
        'phone' => 'Số điện thoại',
        'address' => 'Địa chỉ',
        'status' => 'Trạng thái',
        'created_at' => 'Ngày tạo'
    ];
    
    protected static $excelValidationRules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:20',
        'address' => 'nullable|string|max:500',
        'status' => 'required|in:active,inactive,pending'
    ];
    
    protected static $excelValidationMessages = [
        'name.required' => 'Tên là bắt buộc',
        'email.required' => 'Email là bắt buộc',
        'email.email' => 'Email không hợp lệ',
        'phone.required' => 'Số điện thoại là bắt buộc',
        'status.required' => 'Trạng thái là bắt buộc',
        'status.in' => 'Trạng thái phải là active, inactive hoặc pending'
    ];
    
    protected static $excelUniqueFields = [
        'email'
    ];
    
    protected static $excelModelName = 'ExampleModel';
    
    protected static $excelFieldTypes = [
        'name' => 'string',
        'email' => 'email',
        'phone' => 'string',
        'address' => 'string',
        'status' => 'enum'
    ];
}
