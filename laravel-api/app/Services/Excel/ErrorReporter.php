<?php

namespace App\Services\Excel;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ErrorReporter
{
    protected array $errors = [];
    protected array $warnings = [];
    protected array $info = [];
    protected string $sessionId;
    protected string $modelClass;
    protected string $operation;
    
    public function __construct(string $modelClass, string $operation = 'import')
    {
        $this->modelClass = $modelClass;
        $this->operation = $operation;
        $this->sessionId = uniqid('excel_', true);
    }
    
    /**
     * Add error
     */
    public function addError(string $message, array $context = []): void
    {
        $this->errors[] = [
            'message' => $message,
            'context' => $context,
            'timestamp' => now()->toISOString(),
            'level' => 'error'
        ];
        
        Log::error("Excel {$this->operation} error", [
            'session_id' => $this->sessionId,
            'model' => $this->modelClass,
            'message' => $message,
            'context' => $context
        ]);
    }
    
    /**
     * Add warning
     */
    public function addWarning(string $message, array $context = []): void
    {
        $this->warnings[] = [
            'message' => $message,
            'context' => $context,
            'timestamp' => now()->toISOString(),
            'level' => 'warning'
        ];
        
        Log::warning("Excel {$this->operation} warning", [
            'session_id' => $this->sessionId,
            'model' => $this->modelClass,
            'message' => $message,
            'context' => $context
        ]);
    }
    
    /**
     * Add info
     */
    public function addInfo(string $message, array $context = []): void
    {
        $this->info[] = [
            'message' => $message,
            'context' => $context,
            'timestamp' => now()->toISOString(),
            'level' => 'info'
        ];
        
        Log::info("Excel {$this->operation} info", [
            'session_id' => $this->sessionId,
            'model' => $this->modelClass,
            'message' => $message,
            'context' => $context
        ]);
    }
    
    /**
     * Add validation error
     */
    public function addValidationError(int $row, string $field, string $message, $value = null): void
    {
        $this->addError("Dòng {$row}: {$field} - {$message}", [
            'row' => $row,
            'field' => $field,
            'value' => $value,
            'type' => 'validation'
        ]);
    }
    
    /**
     * Add duplicate error
     */
    public function addDuplicateError(int $row, array $duplicateFields, array $values): void
    {
        $fields = implode(', ', $duplicateFields);
        $this->addError("Dòng {$row}: Bản ghi trùng lặp tại các trường: {$fields}", [
            'row' => $row,
            'duplicate_fields' => $duplicateFields,
            'values' => $values,
            'type' => 'duplicate'
        ]);
    }
    
    /**
     * Add file error
     */
    public function addFileError(string $message, array $context = []): void
    {
        $this->addError("Lỗi file: {$message}", array_merge($context, ['type' => 'file']));
    }
    
    /**
     * Add database error
     */
    public function addDatabaseError(string $message, array $context = []): void
    {
        $this->addError("Lỗi database: {$message}", array_merge($context, ['type' => 'database']));
    }
    
    /**
     * Add memory error
     */
    public function addMemoryError(string $message, array $context = []): void
    {
        $this->addError("Lỗi memory: {$message}", array_merge($context, ['type' => 'memory']));
    }
    
    /**
     * Get all errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    /**
     * Get all warnings
     */
    public function getWarnings(): array
    {
        return $this->warnings;
    }
    
    /**
     * Get all info
     */
    public function getInfo(): array
    {
        return $this->info;
    }
    
    /**
     * Get all messages
     */
    public function getAllMessages(): array
    {
        return array_merge($this->errors, $this->warnings, $this->info);
    }
    
    /**
     * Get error count
     */
    public function getErrorCount(): int
    {
        return count($this->errors);
    }
    
    /**
     * Get warning count
     */
    public function getWarningCount(): int
    {
        return count($this->warnings);
    }
    
    /**
     * Get info count
     */
    public function getInfoCount(): int
    {
        return count($this->info);
    }
    
    /**
     * Check if has errors
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }
    
    /**
     * Check if has warnings
     */
    public function hasWarnings(): bool
    {
        return !empty($this->warnings);
    }
    
    /**
     * Get summary
     */
    public function getSummary(): array
    {
        return [
            'session_id' => $this->sessionId,
            'model' => $this->modelClass,
            'operation' => $this->operation,
            'errors' => $this->getErrorCount(),
            'warnings' => $this->getWarningCount(),
            'info' => $this->getInfoCount(),
            'total' => $this->getErrorCount() + $this->getWarningCount() + $this->getInfoCount(),
            'timestamp' => now()->toISOString()
        ];
    }
    
    /**
     * Generate detailed report
     */
    public function generateReport(): array
    {
        return [
            'summary' => $this->getSummary(),
            'errors' => $this->errors,
            'warnings' => $this->warnings,
            'info' => $this->info,
            'statistics' => $this->getStatistics()
        ];
    }
    
    /**
     * Get statistics
     */
    public function getStatistics(): array
    {
        $errorTypes = [];
        $fieldErrors = [];
        $rowErrors = [];
        
        foreach ($this->errors as $error) {
            $type = $error['context']['type'] ?? 'unknown';
            $errorTypes[$type] = ($errorTypes[$type] ?? 0) + 1;
            
            if (isset($error['context']['field'])) {
                $field = $error['context']['field'];
                $fieldErrors[$field] = ($fieldErrors[$field] ?? 0) + 1;
            }
            
            if (isset($error['context']['row'])) {
                $row = $error['context']['row'];
                $rowErrors[] = $row;
            }
        }
        
        return [
            'error_types' => $errorTypes,
            'field_errors' => $fieldErrors,
            'error_rows' => array_unique($rowErrors),
            'error_rate' => $this->getErrorCount() > 0 ? 
                round(($this->getErrorCount() / max(1, count($rowErrors))) * 100, 2) : 0
        ];
    }
    
    /**
     * Save report to file
     */
    public function saveReport(string $filename = null): string
    {
        $filename = $filename ?: "excel_report_{$this->sessionId}.json";
        $filePath = storage_path('app/excel/reports/' . $filename);
        
        // Ensure directory exists
        $directory = dirname($filePath);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        
        $report = $this->generateReport();
        file_put_contents($filePath, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        return $filePath;
    }
    
    /**
     * Export errors to Excel
     */
    public function exportErrorsToExcel(string $filename = null): string
    {
        $filename = $filename ?: "excel_errors_{$this->sessionId}.xlsx";
        $filePath = storage_path('app/excel/reports/' . $filename);
        
        // Ensure directory exists
        $directory = dirname($filePath);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        
        $data = [];
        $headers = ['Dòng', 'Trường', 'Loại', 'Thông báo', 'Giá trị', 'Thời gian'];
        
        foreach ($this->errors as $error) {
            $data[] = [
                $error['context']['row'] ?? '',
                $error['context']['field'] ?? '',
                $error['context']['type'] ?? 'error',
                $error['message'],
                $error['context']['value'] ?? '',
                $error['timestamp']
            ];
        }
        
        // Use Maatwebsite\Excel to create Excel file
        \Maatwebsite\Excel\Facades\Excel::store(
            new \App\Services\Excel\ErrorExport($data, $headers),
            $filename,
            'local'
        );
        
        return $filePath;
    }
    
    /**
     * Clear all messages
     */
    public function clear(): void
    {
        $this->errors = [];
        $this->warnings = [];
        $this->info = [];
    }
    
    /**
     * Get session ID
     */
    public function getSessionId(): string
    {
        return $this->sessionId;
    }
}

/**
 * Error Export Class
 */
class ErrorExport implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings
{
    protected array $data;
    protected array $headings;
    
    public function __construct(array $data, array $headings)
    {
        $this->data = $data;
        $this->headings = $headings;
    }
    
    public function array(): array
    {
        return $this->data;
    }
    
    public function headings(): array
    {
        return $this->headings;
    }
}
