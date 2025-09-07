<?php

namespace App\Services\Excel;

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class ExcelService
{
    protected DuplicateHandler $duplicateHandler;
    protected ValidationPipeline $validationPipeline;
    protected array $config;
    
    public function __construct()
    {
        $this->config = config('excel');
        $this->duplicateHandler = new DuplicateHandler('');
        $this->validationPipeline = new ValidationPipeline('');
    }
    
    /**
     * Export data to Excel
     */
    public function export(string $modelClass, array $filters = [], string $filename = null): string
    {
        $this->setMemoryLimit();
        
        $query = $modelClass::getExcelExportQuery();
        
        // Apply filters
        if (!empty($filters)) {
            $query = $this->applyFilters($query, $filters);
        }
        
        $data = $query->get();
        $fields = $modelClass::getExcelFields();
        $labels = $modelClass::getExcelFieldLabels();
        
        $exportData = $data->map(function ($item) use ($fields, $labels) {
            $row = [];
            foreach ($fields as $field) {
                $key = $labels[$field] ?? $field;
                $row[$key] = $item->$field ?? '';
            }
            return $row;
        });
        
        $filename = $filename ?: $modelClass::getExcelExportFilename();
        $filePath = $this->config['export_path'] . '/' . $filename;
        
        // Ensure directory exists
        if (!file_exists($this->config['export_path'])) {
            mkdir($this->config['export_path'], 0755, true);
        }
        
        Excel::store(new ExcelExport($exportData, array_keys($exportData->first() ?? [])), $filename, 'local');
        
        return $filePath;
    }
    
    /**
     * Import data from Excel
     */
    public function import(
        string $filePath,
        string $modelClass,
        string $requestClass = null,
        bool $skipDuplicates = false,
        bool $updateExisting = false
    ): array {
        $this->setMemoryLimit();
        
        $this->duplicateHandler = new DuplicateHandler($modelClass, [], $skipDuplicates, $updateExisting);
        $this->validationPipeline = new ValidationPipeline($modelClass);
        
        $results = [
            'success' => false,
            'processed' => 0,
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => [],
            'duplicates' => [],
            'file_path' => $filePath
        ];
        
        try {
            DB::beginTransaction();
            
            $data = Excel::toArray(new ExcelImport(), $filePath);
            $rows = $data[0] ?? [];
            
            if (empty($rows)) {
                throw new \Exception('File Excel trống hoặc không hợp lệ');
            }
            
            // Remove header row
            $headers = array_shift($rows);
            $fieldMapping = $this->getFieldMapping($headers, $modelClass);
            
            // Process data in chunks
            $chunks = array_chunk($rows, $this->config['chunk_size']);
            $processedCount = 0;
            
            foreach ($chunks as $chunkIndex => $chunk) {
                $processedChunk = $this->processChunk(
                    $chunk,
                    $fieldMapping,
                    $modelClass,
                    $requestClass,
                    $processedCount + 1
                );
                
                $results = $this->mergeResults($results, $processedChunk);
                $processedCount += count($chunk);
                
                // Check if we should stop due to too many errors
                if (count($results['errors']) >= $this->config['validation']['max_errors']) {
                    break;
                }
            }
            
            $results['success'] = true;
            $results['processed'] = $processedCount;
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            $results['errors'][] = [
                'type' => 'import_error',
                'message' => 'Lỗi import: ' . $e->getMessage()
            ];
        }
        
        return $results;
    }
    
    /**
     * Process chunk of data
     */
    protected function processChunk(
        array $chunk,
        array $fieldMapping,
        string $modelClass,
        string $requestClass = null,
        int $startRow = 1
    ): array {
        $results = [
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => [],
            'duplicates' => []
        ];
        
        // Map field names
        $mappedData = [];
        foreach ($chunk as $index => $row) {
            $mappedRow = [];
            foreach ($fieldMapping as $excelField => $dbField) {
                $mappedRow[$dbField] = $row[$excelField] ?? null;
            }
            $mappedData[] = $mappedRow;
        }
        
        // Validate data
        if ($requestClass) {
            $validation = $this->validationPipeline->validateWithBulkStoreRequest($mappedData, $requestClass);
        } else {
            $validation = $this->validationPipeline->validateBatch($mappedData, $startRow);
        }
        
        $results['errors'] = $validation['errors'];
        $validData = $validation['valid'];
        
        if (empty($validData)) {
            return $results;
        }
        
        // Check duplicates
        $duplicateCheck = $this->duplicateHandler->checkDuplicates($validData, $startRow);
        $results['duplicates'] = $duplicateCheck['duplicates'];
        $results['errors'] = array_merge($results['errors'], $duplicateCheck['errors']);
        
        // Handle duplicates
        $duplicateResults = $this->duplicateHandler->handleDuplicates();
        $results['skipped'] = count($duplicateResults['skipped']);
        $results['updated'] = count($duplicateResults['updated']);
        $results['errors'] = array_merge($results['errors'], $duplicateResults['errors']);
        
        // Create new records
        $newData = array_filter($validData, function($row) use ($duplicateCheck) {
            return !$this->isDuplicate($row, $duplicateCheck['duplicates']);
        });
        
        if (!empty($newData)) {
            $this->createRecords($newData, $modelClass);
            $results['created'] = count($newData);
        }
        
        return $results;
    }
    
    /**
     * Create records in database
     */
    protected function createRecords(array $data, string $modelClass): void
    {
        $chunks = array_chunk($data, $this->config['import']['batch_size']);
        
        foreach ($chunks as $chunk) {
            $modelClass::insert($chunk);
        }
    }
    
    /**
     * Check if row is duplicate
     */
    protected function isDuplicate(array $row, array $duplicates): bool
    {
        foreach ($duplicates as $duplicate) {
            if ($duplicate['data'] === $row) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Get field mapping from Excel headers
     */
    protected function getFieldMapping(array $headers, string $modelClass): array
    {
        $fieldLabels = $modelClass::getExcelFieldLabels();
        $mapping = [];
        
        foreach ($headers as $header) {
            $field = array_search($header, $fieldLabels);
            if ($field !== false) {
                $mapping[$header] = $field;
            } else {
                // Try to find by field name
                $mapping[$header] = $header;
            }
        }
        
        return $mapping;
    }
    
    /**
     * Apply filters to query
     */
    protected function applyFilters($query, array $filters)
    {
        foreach ($filters as $field => $value) {
            if (is_array($value)) {
                $query->whereIn($field, $value);
            } else {
                $query->where($field, $value);
            }
        }
        
        return $query;
    }
    
    /**
     * Set memory limit
     */
    protected function setMemoryLimit(): void
    {
        ini_set('memory_limit', $this->config['memory_limit']);
        set_time_limit($this->config['max_execution_time']);
    }
    
    /**
     * Merge results from different chunks
     */
    protected function mergeResults(array $results, array $chunkResults): array
    {
        $results['created'] += $chunkResults['created'];
        $results['updated'] += $chunkResults['updated'];
        $results['skipped'] += $chunkResults['skipped'];
        $results['errors'] = array_merge($results['errors'], $chunkResults['errors']);
        $results['duplicates'] = array_merge($results['duplicates'], $chunkResults['duplicates']);
        
        return $results;
    }
    
    /**
     * Generate template file
     */
    public function generateTemplate(string $modelClass): string
    {
        $fields = $modelClass::getExcelImportFields();
        $labels = $modelClass::getExcelFieldLabels();
        $requiredFields = $modelClass::getExcelRequiredFields();
        
        $templateData = collect([$labels]);
        
        $filename = $modelClass::getExcelTemplateFilename();
        $filePath = $this->config['export_path'] . '/' . $filename;
        
        Excel::store(new ExcelTemplate($templateData, $requiredFields), $filename, 'local');
        
        return $filePath;
    }
    
    /**
     * Get import statistics
     */
    public function getImportStatistics(): array
    {
        return [
            'validation' => $this->validationPipeline->getStatistics(),
            'duplicates' => $this->duplicateHandler->getStatistics()
        ];
    }
}

/**
 * Excel Export Class
 */
class ExcelExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithEvents
{
    protected Collection $data;
    protected array $headings;
    
    public function __construct(Collection $data, array $headings)
    {
        $this->data = $data;
        $this->headings = $headings;
    }
    
    public function collection()
    {
        return $this->data;
    }
    
    public function headings(): array
    {
        return $this->headings;
    }
    
    public function styles(Worksheet $sheet)
    {
        $config = config('excel.export.styles');
        
        return [
            1 => [
                'font' => $config['header']['font'],
                'fill' => $config['header']['fill'],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
    
    public function columnWidths(): array
    {
        $widths = [];
        foreach ($this->headings as $index => $heading) {
            $widths[chr(65 + $index)] = 20; // A, B, C, etc.
        }
        return $widths;
    }
    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Freeze first row
                $sheet->freezePane('A2');
                
                // Auto size columns
                foreach (range('A', $sheet->getHighestColumn()) as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
}

/**
 * Excel Import Class
 */
class ExcelImport
{
    // This class is used by Maatwebsite\Excel for import
}

/**
 * Excel Template Class
 */
class ExcelTemplate implements FromCollection, WithHeadings, WithStyles
{
    protected Collection $data;
    protected array $requiredFields;
    
    public function __construct(Collection $data, array $requiredFields)
    {
        $this->data = $data;
        $this->requiredFields = $requiredFields;
    }
    
    public function collection()
    {
        return $this->data;
    }
    
    public function headings(): array
    {
        return array_keys($this->data->first() ?? []);
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']],
            ],
        ];
    }
}
