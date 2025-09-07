<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Excel Import/Export Configuration
    |--------------------------------------------------------------------------
    */
    
    'memory_limit' => env('EXCEL_MEMORY_LIMIT', '512M'),
    'max_execution_time' => env('EXCEL_MAX_EXECUTION_TIME', 300),
    'chunk_size' => env('EXCEL_CHUNK_SIZE', 1000),
    'max_rows' => env('EXCEL_MAX_ROWS', 10000),
    'temp_path' => storage_path('app/excel/temp'),
    'export_path' => storage_path('app/excel/exports'),
    
    /*
    |--------------------------------------------------------------------------
    | Queue Configuration
    |--------------------------------------------------------------------------
    */
    'use_queue' => env('EXCEL_USE_QUEUE', true),
    'queue_name' => env('EXCEL_QUEUE_NAME', 'excel'),
    'queue_connection' => env('EXCEL_QUEUE_CONNECTION', 'redis'),
    
    /*
    |--------------------------------------------------------------------------
    | Validation Configuration
    |--------------------------------------------------------------------------
    */
    'validation' => [
        'stop_on_first_error' => false,
        'max_errors' => 100,
        'custom_rules' => [
            'duplicate_check' => true,
            'required_fields' => true,
            'data_type_validation' => true,
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | File Configuration
    |--------------------------------------------------------------------------
    */
    'file' => [
        'allowed_extensions' => ['xlsx', 'xls', 'csv'],
        'max_file_size' => 10240, // KB
        'encoding' => 'UTF-8',
        'delimiter' => ',',
        'enclosure' => '"',
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Export Configuration
    |--------------------------------------------------------------------------
    */
    'export' => [
        'default_format' => 'xlsx',
        'include_headers' => true,
        'auto_size_columns' => true,
        'freeze_first_row' => true,
        'styles' => [
            'header' => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'CCCCCC']],
            ],
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Import Configuration
    |--------------------------------------------------------------------------
    */
    'import' => [
        'skip_empty_rows' => true,
        'skip_duplicates' => false,
        'update_existing' => false,
        'batch_size' => 500,
        'progress_callback' => true,
    ],
];
