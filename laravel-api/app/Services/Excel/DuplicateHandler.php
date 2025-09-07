<?php

namespace App\Services\Excel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DuplicateHandler
{
    protected array $duplicates = [];
    protected array $errors = [];
    protected int $processedCount = 0;
    protected int $duplicateCount = 0;
    
    public function __construct(
        protected string $modelClass,
        protected array $uniqueFields = [],
        protected bool $skipDuplicates = false,
        protected bool $updateExisting = false
    ) {
        $this->uniqueFields = $this->modelClass::getExcelUniqueFields();
    }
    
    /**
     * Check for duplicates in a batch of data
     */
    public function checkDuplicates(array $data, int $startRow = 1): array
    {
        $this->processedCount += count($data);
        $duplicates = [];
        $errors = [];
        
        foreach ($data as $index => $row) {
            $rowNumber = $startRow + $index;
            $duplicate = $this->findDuplicate($row);
            
            if ($duplicate) {
                $duplicates[] = [
                    'row' => $rowNumber,
                    'data' => $row,
                    'existing' => $duplicate,
                    'fields' => $this->getDuplicateFields($row, $duplicate)
                ];
                
                $this->duplicateCount++;
                
                if (!$this->skipDuplicates) {
                    $errors[] = $this->createDuplicateError($rowNumber, $row, $duplicate);
                }
            }
        }
        
        $this->duplicates = array_merge($this->duplicates, $duplicates);
        
        return [
            'duplicates' => $duplicates,
            'errors' => $errors,
            'processed' => count($data),
            'duplicate_count' => count($duplicates)
        ];
    }
    
    /**
     * Find duplicate record
     */
    protected function findDuplicate(array $data): ?Model
    {
        if (empty($this->uniqueFields)) {
            return null;
        }
        
        $query = $this->modelClass::getExcelImportQuery();
        
        foreach ($this->uniqueFields as $field) {
            if (isset($data[$field]) && !empty($data[$field])) {
                $query->where($field, $data[$field]);
            } else {
                // If any unique field is empty, no duplicate possible
                return null;
            }
        }
        
        return $query->first();
    }
    
    /**
     * Get fields that caused the duplicate
     */
    protected function getDuplicateFields(array $newData, Model $existing): array
    {
        $duplicateFields = [];
        
        foreach ($this->uniqueFields as $field) {
            if (isset($newData[$field]) && $newData[$field] == $existing->$field) {
                $duplicateFields[] = $field;
            }
        }
        
        return $duplicateFields;
    }
    
    /**
     * Create duplicate error message
     */
    protected function createDuplicateError(int $rowNumber, array $data, Model $existing): array
    {
        $duplicateFields = $this->getDuplicateFields($data, $existing);
        $fieldLabels = $this->modelClass::getExcelFieldLabels();
        
        $fieldNames = array_map(function($field) use ($fieldLabels) {
            return $fieldLabels[$field] ?? $field;
        }, $duplicateFields);
        
        return [
            'row' => $rowNumber,
            'type' => 'duplicate',
            'message' => "Bản ghi trùng lặp tại dòng {$rowNumber}. Các trường: " . implode(', ', $fieldNames),
            'fields' => $duplicateFields,
            'values' => array_intersect_key($data, array_flip($duplicateFields)),
            'existing_id' => $existing->id
        ];
    }
    
    /**
     * Handle duplicates based on configuration
     */
    public function handleDuplicates(): array
    {
        $results = [
            'skipped' => [],
            'updated' => [],
            'errors' => []
        ];
        
        foreach ($this->duplicates as $duplicate) {
            if ($this->skipDuplicates) {
                $results['skipped'][] = $duplicate;
            } elseif ($this->updateExisting) {
                try {
                    $this->updateExistingRecord($duplicate);
                    $results['updated'][] = $duplicate;
                } catch (\Exception $e) {
                    $results['errors'][] = [
                        'row' => $duplicate['row'],
                        'message' => "Lỗi cập nhật bản ghi: " . $e->getMessage(),
                        'data' => $duplicate['data']
                    ];
                }
            } else {
                $results['errors'][] = $this->createDuplicateError(
                    $duplicate['row'],
                    $duplicate['data'],
                    $duplicate['existing']
                );
            }
        }
        
        return $results;
    }
    
    /**
     * Update existing record
     */
    protected function updateExistingRecord(array $duplicate): void
    {
        $existing = $duplicate['existing'];
        $newData = $duplicate['data'];
        
        // Remove unique fields from update data
        $updateData = array_diff_key($newData, array_flip($this->uniqueFields));
        
        if (!empty($updateData)) {
            $existing->update($updateData);
        }
    }
    
    /**
     * Get statistics
     */
    public function getStatistics(): array
    {
        return [
            'processed' => $this->processedCount,
            'duplicates' => $this->duplicateCount,
            'duplicate_rate' => $this->processedCount > 0 ? 
                round(($this->duplicateCount / $this->processedCount) * 100, 2) : 0
        ];
    }
    
    /**
     * Reset handler state
     */
    public function reset(): void
    {
        $this->duplicates = [];
        $this->errors = [];
        $this->processedCount = 0;
        $this->duplicateCount = 0;
    }
    
    /**
     * Get duplicate records
     */
    public function getDuplicates(): array
    {
        return $this->duplicates;
    }
    
    /**
     * Get errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    /**
     * Check if has duplicates
     */
    public function hasDuplicates(): bool
    {
        return !empty($this->duplicates);
    }
    
    /**
     * Get duplicate count
     */
    public function getDuplicateCount(): int
    {
        return $this->duplicateCount;
    }
    
    /**
     * Set configuration
     */
    public function setConfig(bool $skipDuplicates = false, bool $updateExisting = false): void
    {
        $this->skipDuplicates = $skipDuplicates;
        $this->updateExisting = $updateExisting;
    }
}
