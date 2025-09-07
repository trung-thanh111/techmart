<?php

namespace App\Services\Excel;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ValidationPipeline
{
    protected array $errors = [];
    protected int $processedCount = 0;
    protected int $errorCount = 0;
    protected array $validData = [];
    protected array $invalidData = [];
    
    public function __construct(
        protected string $modelClass,
        protected array $rules = [],
        protected array $messages = [],
        protected bool $stopOnFirstError = false,
        protected int $maxErrors = 100
    ) {
        $this->rules = $this->modelClass::getExcelValidationRules();
        $this->messages = $this->modelClass::getExcelValidationMessages();
    }
    
    /**
     * Validate batch of data
     */
    public function validateBatch(array $data, int $startRow = 1): array
    {
        $this->processedCount += count($data);
        $batchErrors = [];
        $batchValidData = [];
        $batchInvalidData = [];
        
        foreach ($data as $index => $row) {
            $rowNumber = $startRow + $index;
            $validation = $this->validateRow($row, $rowNumber);
            
            if ($validation['valid']) {
                $batchValidData[] = $validation['data'];
            } else {
                $batchErrors = array_merge($batchErrors, $validation['errors']);
                $batchInvalidData[] = $validation['data'];
                
                if ($this->stopOnFirstError || $this->errorCount >= $this->maxErrors) {
                    break;
                }
            }
        }
        
        $this->errors = array_merge($this->errors, $batchErrors);
        $this->errorCount += count($batchErrors);
        $this->validData = array_merge($this->validData, $batchValidData);
        $this->invalidData = array_merge($this->invalidData, $batchInvalidData);
        
        return [
            'valid' => $batchValidData,
            'invalid' => $batchInvalidData,
            'errors' => $batchErrors,
            'processed' => count($data),
            'error_count' => count($batchErrors)
        ];
    }
    
    /**
     * Validate single row
     */
    protected function validateRow(array $data, int $rowNumber): array
    {
        try {
            $validator = Validator::make($data, $this->rules, $this->messages);
            
            if ($validator->fails()) {
                $errors = $this->formatValidationErrors($validator->errors(), $rowNumber);
                return [
                    'valid' => false,
                    'data' => $data,
                    'errors' => $errors
                ];
            }
            
            return [
                'valid' => true,
                'data' => $data,
                'errors' => []
            ];
            
        } catch (\Exception $e) {
            return [
                'valid' => false,
                'data' => $data,
                'errors' => [[
                    'row' => $rowNumber,
                    'type' => 'validation_error',
                    'message' => "Lỗi validation: " . $e->getMessage(),
                    'field' => 'general'
                ]]
            ];
        }
    }
    
    /**
     * Format validation errors
     */
    protected function formatValidationErrors($errors, int $rowNumber): array
    {
        $formattedErrors = [];
        
        foreach ($errors->toArray() as $field => $messages) {
            foreach ($messages as $message) {
                $formattedErrors[] = [
                    'row' => $rowNumber,
                    'type' => 'validation',
                    'message' => $message,
                    'field' => $field,
                    'value' => $errors->getData()[$field] ?? null
                ];
            }
        }
        
        return $formattedErrors;
    }
    
    /**
     * Validate using BulkStoreRequest
     */
    public function validateWithBulkStoreRequest(array $data, string $requestClass): array
    {
        $validData = [];
        $errors = [];
        
        foreach ($data as $index => $row) {
            try {
                $request = new $requestClass();
                $request->setContainer(app());
                $request->setRedirector(app('redirect'));
                
                // Set data for validation
                $request->replace($row);
                
                // Validate
                $validator = app('validator')->make($row, $request->rules(), $request->messages());
                
                if ($validator->fails()) {
                    $errors = array_merge($errors, $this->formatValidationErrors($validator->errors(), $index + 1));
                } else {
                    $validData[] = $row;
                }
                
            } catch (\Exception $e) {
                $errors[] = [
                    'row' => $index + 1,
                    'type' => 'request_validation_error',
                    'message' => "Lỗi validation request: " . $e->getMessage(),
                    'field' => 'general'
                ];
            }
        }
        
        return [
            'valid' => $validData,
            'errors' => $errors,
            'processed' => count($data),
            'error_count' => count($errors)
        ];
    }
    
    /**
     * Get validation statistics
     */
    public function getStatistics(): array
    {
        return [
            'processed' => $this->processedCount,
            'valid' => count($this->validData),
            'invalid' => count($this->invalidData),
            'errors' => $this->errorCount,
            'error_rate' => $this->processedCount > 0 ? 
                round(($this->errorCount / $this->processedCount) * 100, 2) : 0
        ];
    }
    
    /**
     * Get valid data
     */
    public function getValidData(): array
    {
        return $this->validData;
    }
    
    /**
     * Get invalid data
     */
    public function getInvalidData(): array
    {
        return $this->invalidData;
    }
    
    /**
     * Get all errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    /**
     * Check if has errors
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }
    
    /**
     * Get error count
     */
    public function getErrorCount(): int
    {
        return $this->errorCount;
    }
    
    /**
     * Reset pipeline
     */
    public function reset(): void
    {
        $this->errors = [];
        $this->processedCount = 0;
        $this->errorCount = 0;
        $this->validData = [];
        $this->invalidData = [];
    }
    
    /**
     * Set configuration
     */
    public function setConfig(
        bool $stopOnFirstError = false,
        int $maxErrors = 100
    ): void {
        $this->stopOnFirstError = $stopOnFirstError;
        $this->maxErrors = $maxErrors;
    }
    
    /**
     * Get field validation rules
     */
    public function getFieldRules(string $field): array
    {
        return $this->rules[$field] ?? [];
    }
    
    /**
     * Add custom validation rule
     */
    public function addCustomRule(string $field, $rule): void
    {
        if (!isset($this->rules[$field])) {
            $this->rules[$field] = [];
        }
        
        if (is_array($rule)) {
            $this->rules[$field] = array_merge($this->rules[$field], $rule);
        } else {
            $this->rules[$field][] = $rule;
        }
    }
}
