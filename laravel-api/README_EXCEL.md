# Hệ thống Import/Export Excel

Hệ thống import/export Excel được thiết kế để xử lý dữ liệu Excel một cách hiệu quả và linh hoạt cho Laravel.

## Tính năng chính

- ✅ **Generic System**: Có thể dùng cho nhiều model, field tùy chỉnh
- ✅ **Export**: Tự động download file Excel với styling đẹp
- ✅ **Import**: Validate từ BulkStoreRequest của từng model
- ✅ **Duplicate Check**: Xử lý lỗi trùng lặp thông minh
- ✅ **Config**: Memory, limit, record settings
- ✅ **Queue Jobs**: Xử lý file lớn trong background
- ✅ **Error Reporting**: Báo cáo lỗi chi tiết
- ✅ **Frontend**: Component Nuxt 4 với loading state

## Cài đặt

### 1. Cài đặt package

```bash
composer require maatwebsite/excel
```

### 2. Publish config

```bash
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider" --tag=config
```

### 3. Tạo thư mục cần thiết

```bash
mkdir -p storage/app/excel/{temp,exports,imports,reports}
chmod 755 storage/app/excel/{temp,exports,imports,reports}
```

## Sử dụng

### 1. Cấu hình Model

Thêm trait `Excelable` vào model và cấu hình:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Excelable;

class User extends Model
{
    use Excelable;
    
    // Các field sẽ export
    protected static $excelFields = [
        'id', 'name', 'email', 'phone', 'created_at'
    ];
    
    // Các field sẽ import
    protected static $excelImportFields = [
        'name', 'email', 'phone'
    ];
    
    // Label cho header Excel
    protected static $excelFieldLabels = [
        'id' => 'ID',
        'name' => 'Tên',
        'email' => 'Email',
        'phone' => 'Số điện thoại',
        'created_at' => 'Ngày tạo'
    ];
    
    // Validation rules
    protected static $excelValidationRules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:20'
    ];
    
    // Custom validation messages
    protected static $excelValidationMessages = [
        'name.required' => 'Tên là bắt buộc',
        'email.required' => 'Email là bắt buộc',
        'email.email' => 'Email không hợp lệ'
    ];
    
    // Các field unique để check duplicate
    protected static $excelUniqueFields = [
        'email'
    ];
    
    // Tên model cho Excel
    protected static $excelModelName = 'User';
}
```

### 2. Tạo BulkStoreRequest

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserBulkStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'required|string|max:20'
        ];
    }
    
    public function messages(): array
    {
        return [
            'name.required' => 'Tên là bắt buộc',
            'email.required' => 'Email là bắt buộc',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email đã tồn tại'
        ];
    }
}
```

### 3. Sử dụng Controller

```php
<?php

namespace App\Http\Controllers;

use App\Services\Excel\ExcelService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected ExcelService $excelService;
    
    public function __construct(ExcelService $excelService)
    {
        $this->excelService = $excelService;
    }
    
    public function export(Request $request)
    {
        $filters = $request->input('filters', []);
        $filePath = $this->excelService->export('App\\Models\\User', $filters);
        
        return response()->download($filePath);
    }
    
    public function import(Request $request)
    {
        $file = $request->file('file');
        $filePath = $file->store('excel/imports', 'local');
        
        $results = $this->excelService->import(
            storage_path('app/' . $filePath),
            'App\\Models\\User',
            'App\\Http\\Requests\\UserBulkStoreRequest',
            false, // skip duplicates
            false  // update existing
        );
        
        return response()->json($results);
    }
}
```

### 4. Sử dụng Frontend Component

```vue
<template>
  <div>
    <ExcelButtons
      model="user"
      :filters="{ status: 'active' }"
      request-class="App\\Http\\Requests\\UserBulkStoreRequest"
      :skip-duplicates="false"
      :update-existing="false"
      :use-queue="true"
      @export="handleExport"
      @import="handleImport"
      @error="handleError"
    />
  </div>
</template>

<script setup>
const handleExport = (data) => {
  console.log('Export completed:', data);
};

const handleImport = (data) => {
  console.log('Import completed:', data);
};

const handleError = (error) => {
  console.error('Error:', error);
};
</script>
```

## API Endpoints

### Export
```http
POST /api/excel/export
Content-Type: application/json

{
    "model": "user",
    "filters": {
        "status": "active"
    },
    "filename": "users_export.xlsx"
}
```

### Import
```http
POST /api/excel/import
Content-Type: multipart/form-data

file: [Excel file]
model: "user"
request_class: "App\\Http\\Requests\\UserBulkStoreRequest"
skip_duplicates: false
update_existing: false
```

### Template
```http
POST /api/excel/template
Content-Type: application/json

{
    "model": "user"
}
```

### Download
```http
GET /api/excel/download/{filename}
```

## Cấu hình

### config/excel.php

```php
<?php

return [
    'memory_limit' => env('EXCEL_MEMORY_LIMIT', '512M'),
    'max_execution_time' => env('EXCEL_MAX_EXECUTION_TIME', 300),
    'chunk_size' => env('EXCEL_CHUNK_SIZE', 1000),
    'max_rows' => env('EXCEL_MAX_ROWS', 10000),
    'use_queue' => env('EXCEL_USE_QUEUE', true),
    'queue_name' => env('EXCEL_QUEUE_NAME', 'excel'),
    
    'validation' => [
        'stop_on_first_error' => false,
        'max_errors' => 100,
    ],
    
    'import' => [
        'skip_empty_rows' => true,
        'skip_duplicates' => false,
        'update_existing' => false,
        'batch_size' => 500,
    ],
];
```

## Queue Jobs

### Export Job
```php
use App\Jobs\ExportExcelJob;

ExportExcelJob::dispatch(
    'App\\Models\\User',
    ['status' => 'active'],
    'users_export.xlsx',
    auth()->id(),
    'database'
);
```

### Import Job
```php
use App\Jobs\ImportExcelJob;

ImportExcelJob::dispatch(
    $filePath,
    'App\\Models\\User',
    'App\\Http\\Requests\\UserBulkStoreRequest',
    false, // skip duplicates
    false, // update existing
    auth()->id(),
    'database'
);
```

## Error Handling

Hệ thống có error reporting chi tiết:

```php
use App\Services\Excel\ErrorReporter;

$errorReporter = new ErrorReporter('App\\Models\\User', 'import');

// Add errors
$errorReporter->addValidationError(1, 'email', 'Email không hợp lệ', 'invalid-email');
$errorReporter->addDuplicateError(2, ['email'], ['email' => 'test@example.com']);

// Get report
$report = $errorReporter->generateReport();

// Save report
$filePath = $errorReporter->saveReport();
```

## Performance Tips

1. **Memory Management**: Sử dụng chunk processing cho file lớn
2. **Queue**: Dùng queue cho file > 1000 records
3. **Database**: Batch insert/update
4. **Validation**: Validate theo chunk để tránh timeout
5. **Caching**: Cache validation rules

## Troubleshooting

### Lỗi memory limit
```bash
# Tăng memory limit
php artisan config:cache
```

### Lỗi timeout
```bash
# Tăng max execution time
ini_set('max_execution_time', 600);
```

### Lỗi queue
```bash
# Chạy queue worker
php artisan queue:work --queue=excel
```

## License

MIT
