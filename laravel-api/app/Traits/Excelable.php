<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait Excelable
{
    /**
     * Get exportable fields configuration
     */
    public static function getExcelFields(): array
    {
        return static::$excelFields ?? [];
    }
    
    /**
     * Get importable fields configuration
     */
    public static function getExcelImportFields(): array
    {
        return static::$excelImportFields ?? static::getExcelFields();
    }
    
    /**
     * Get field labels for export headers
     */
    public static function getExcelFieldLabels(): array
    {
        return static::$excelFieldLabels ?? [];
    }
    
    /**
     * Get validation rules for import
     */
    public static function getExcelValidationRules(): array
    {
        return static::$excelValidationRules ?? [];
    }
    
    /**
     * Get unique fields for duplicate checking
     */
    public static function getExcelUniqueFields(): array
    {
        return static::$excelUniqueFields ?? [];
    }
    
    /**
     * Get model name for Excel operations
     */
    public static function getExcelModelName(): string
    {
        return static::$excelModelName ?? class_basename(static::class);
    }
    
    /**
     * Get export query builder
     */
    public static function getExcelExportQuery()
    {
        return static::query();
    }
    
    /**
     * Get import query builder for duplicate checking
     */
    public static function getExcelImportQuery()
    {
        return static::query();
    }
    
    /**
     * Process data before export
     */
    public function processForExcelExport(): array
    {
        $data = $this->toArray();
        $fields = static::getExcelFields();
        $labels = static::getExcelFieldLabels();
        
        $result = [];
        foreach ($fields as $field) {
            $key = $labels[$field] ?? $field;
            $result[$key] = $data[$field] ?? '';
        }
        
        return $result;
    }
    
    /**
     * Process data before import
     */
    public static function processForExcelImport(array $data): array
    {
        $fields = static::getExcelImportFields();
        $result = [];
        
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $result[$field] = $data[$field];
            }
        }
        
        return $result;
    }
    
    /**
     * Check if record exists (for duplicate checking)
     */
    public static function checkExcelDuplicate(array $data): ?Model
    {
        $uniqueFields = static::getExcelUniqueFields();
        
        if (empty($uniqueFields)) {
            return null;
        }
        
        $query = static::getExcelImportQuery();
        
        foreach ($uniqueFields as $field) {
            if (isset($data[$field])) {
                $query->where($field, $data[$field]);
            }
        }
        
        return $query->first();
    }
    
    /**
     * Get Excel export filename
     */
    public static function getExcelExportFilename(): string
    {
        $modelName = static::getExcelModelName();
        $timestamp = now()->format('Y-m-d_H-i-s');
        
        return "{$modelName}_export_{$timestamp}.xlsx";
    }
    
    /**
     * Get Excel import template filename
     */
    public static function getExcelTemplateFilename(): string
    {
        $modelName = static::getExcelModelName();
        
        return "{$modelName}_template.xlsx";
    }
    
    /**
     * Get field mapping for import
     */
    public static function getExcelFieldMapping(): array
    {
        $fields = static::getExcelImportFields();
        $labels = static::getExcelFieldLabels();
        
        $mapping = [];
        foreach ($fields as $field) {
            $mapping[$labels[$field] ?? $field] = $field;
        }
        
        return $mapping;
    }
    
    /**
     * Get required fields for import
     */
    public static function getExcelRequiredFields(): array
    {
        $rules = static::getExcelValidationRules();
        $required = [];
        
        foreach ($rules as $field => $rule) {
            if (is_string($rule) && str_contains($rule, 'required')) {
                $required[] = $field;
            } elseif (is_array($rule) && in_array('required', $rule)) {
                $required[] = $field;
            }
        }
        
        return $required;
    }
    
    /**
     * Get field types for validation
     */
    public static function getExcelFieldTypes(): array
    {
        return static::$excelFieldTypes ?? [];
    }
    
    /**
     * Get custom validation messages
     */
    public static function getExcelValidationMessages(): array
    {
        return static::$excelValidationMessages ?? [];
    }
}
