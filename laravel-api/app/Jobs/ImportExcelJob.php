<?php

namespace App\Jobs;

use App\Services\Excel\ExcelService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImportExcelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected string $filePath;
    protected string $modelClass;
    protected string $requestClass;
    protected bool $skipDuplicates;
    protected bool $updateExisting;
    protected string $userId;
    protected string $notificationChannel;
    
    public $timeout = 600;
    public $tries = 3;
    
    public function __construct(
        string $filePath,
        string $modelClass,
        string $requestClass = null,
        bool $skipDuplicates = false,
        bool $updateExisting = false,
        string $userId = null,
        string $notificationChannel = 'database'
    ) {
        $this->filePath = $filePath;
        $this->modelClass = $modelClass;
        $this->requestClass = $requestClass;
        $this->skipDuplicates = $skipDuplicates;
        $this->updateExisting = $updateExisting;
        $this->userId = $userId;
        $this->notificationChannel = $notificationChannel;
        
        $this->onQueue(config('excel.queue_name', 'excel'));
    }
    
    public function handle(ExcelService $excelService): void
    {
        try {
            Log::info("Starting Excel import for model: {$this->modelClass}");
            
            $results = $excelService->import(
                $this->filePath,
                $this->modelClass,
                $this->requestClass,
                $this->skipDuplicates,
                $this->updateExisting
            );
            
            $this->notifySuccess($results);
            
            Log::info("Excel import completed", $results);
            
        } catch (\Exception $e) {
            Log::error("Excel import failed: " . $e->getMessage());
            $this->notifyError($e->getMessage());
            throw $e;
        } finally {
            // Clean up file
            if (file_exists($this->filePath)) {
                unlink($this->filePath);
            }
        }
    }
    
    protected function notifySuccess(array $results): void
    {
        $message = "Import Excel thành công. ";
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
        
        $data = [
            'type' => 'excel_import_success',
            'message' => $message,
            'results' => $results,
            'model' => $this->modelClass,
            'created_at' => now()
        ];
        
        if ($this->userId) {
            $this->notifyUser($data);
        }
    }
    
    protected function notifyError(string $error): void
    {
        $data = [
            'type' => 'excel_import_error',
            'message' => "Import Excel thất bại: {$error}",
            'model' => $this->modelClass,
            'error' => $error,
            'created_at' => now()
        ];
        
        if ($this->userId) {
            $this->notifyUser($data);
        }
    }
    
    protected function notifyUser(array $data): void
    {
        // Implement notification logic based on channel
        switch ($this->notificationChannel) {
            case 'database':
                $this->createDatabaseNotification($data);
                break;
            case 'email':
                $this->sendEmailNotification($data);
                break;
            case 'broadcast':
                $this->broadcastNotification($data);
                break;
        }
    }
    
    protected function createDatabaseNotification(array $data): void
    {
        // Create database notification
        Log::info("Database notification created", $data);
    }
    
    protected function sendEmailNotification(array $data): void
    {
        // Send email notification
        Log::info("Email notification sent", $data);
    }
    
    protected function broadcastNotification(array $data): void
    {
        // Broadcast notification
        Log::info("Broadcast notification sent", $data);
    }
    
    public function failed(\Throwable $exception): void
    {
        Log::error("Excel import job failed permanently", [
            'model' => $this->modelClass,
            'file_path' => $this->filePath,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
        
        $this->notifyError($exception->getMessage());
        
        // Clean up file on failure
        if (file_exists($this->filePath)) {
            unlink($this->filePath);
        }
    }
}
