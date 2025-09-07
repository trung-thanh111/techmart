<?php

namespace App\Http\Controllers;

use App\Services\Excel\ExcelService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ExcelController extends Controller
{
    protected ExcelService $excelService;
    
    public function __construct(ExcelService $excelService)
    {
        $this->excelService = $excelService;
    }
    
    /**
     * Export data to Excel
     */
    public function export(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'model' => 'required|string',
            'filters' => 'sometimes|array',
            'filename' => 'sometimes|string|max:255'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $modelClass = $this->getModelClass($request->input('model'));
            $filters = $request->input('filters', []);
            $filename = $request->input('filename');
            
            $filePath = $this->excelService->export($modelClass, $filters, $filename);
            
            return response()->json([
                'success' => true,
                'message' => 'Export thành công',
                'file_path' => $filePath,
                'download_url' => $this->getDownloadUrl($filePath)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi export: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Import data from Excel
     */
    public function import(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'model' => 'required|string',
            'request_class' => 'sometimes|string',
            'skip_duplicates' => 'sometimes|boolean',
            'update_existing' => 'sometimes|boolean'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $file = $request->file('file');
            $modelClass = $this->getModelClass($request->input('model'));
            $requestClass = $request->input('request_class');
            $skipDuplicates = $request->input('skip_duplicates', false);
            $updateExisting = $request->input('update_existing', false);
            
            // Store uploaded file
            $filePath = $file->store('excel/imports', 'local');
            $fullPath = storage_path('app/' . $filePath);
            
            // Import data
            $results = $this->excelService->import(
                $fullPath,
                $modelClass,
                $requestClass,
                $skipDuplicates,
                $updateExisting
            );
            
            // Clean up uploaded file
            Storage::delete($filePath);
            
            return response()->json([
                'success' => $results['success'],
                'message' => $this->getImportMessage($results),
                'data' => $results
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi import: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Download exported file
     */
    public function download(string $filename): Response
    {
        $filePath = config('excel.export_path') . '/' . $filename;
        
        if (!file_exists($filePath)) {
            abort(404, 'File không tồn tại');
        }
        
        return response()->download($filePath, $filename);
    }
    
    /**
     * Generate template file
     */
    public function template(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'model' => 'required|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $modelClass = $this->getModelClass($request->input('model'));
            $filePath = $this->excelService->generateTemplate($modelClass);
            
            return response()->json([
                'success' => true,
                'message' => 'Tạo template thành công',
                'file_path' => $filePath,
                'download_url' => $this->getDownloadUrl($filePath)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi tạo template: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get import statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = $this->excelService->getImportStatistics();
            
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi lấy thống kê: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get model class from string
     */
    protected function getModelClass(string $model): string
    {
        $modelClass = 'App\\Models\\' . Str::studly($model);
        
        if (!class_exists($modelClass)) {
            throw new \Exception("Model {$modelClass} không tồn tại");
        }
        
        if (!in_array('App\\Traits\\Excelable', class_uses($modelClass))) {
            throw new \Exception("Model {$modelClass} phải sử dụng trait Excelable");
        }
        
        return $modelClass;
    }
    
    /**
     * Get download URL
     */
    protected function getDownloadUrl(string $filePath): string
    {
        $filename = basename($filePath);
        return route('excel.download', ['filename' => $filename]);
    }
    
    /**
     * Get import message based on results
     */
    protected function getImportMessage(array $results): string
    {
        if (!$results['success']) {
            return 'Import thất bại';
        }
        
        $message = "Import thành công. ";
        $message .= "Đã xử lý: {$results['processed']} bản ghi. ";
        
        if ($results['created'] > 0) {
            $message .= "Tạo mới: {$results['created']}. ";
        }
        
        if ($results['updated'] > 0) {
            $message .= "Cập nhật: {$results['updated']}. ";
        }
        
        if ($results['skipped'] > 0) {
            $message .= "Bỏ qua: {$results['skipped']}. ";
        }
        
        if (!empty($results['errors'])) {
            $message .= "Lỗi: " . count($results['errors']) . " bản ghi.";
        }
        
        return $message;
    }
}
