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

class ExportExcelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected string $modelClass;
    protected array $filters;
    protected string $filename;
    protected string $userId;
    protected string $notificationChannel;
    
    public $timeout = 300;
    public $tries = 3;
    
    public function __construct(
        string $modelClass,
        array $filters = [],
        string $filename = null,
        string $userId = null,
        string $notificationChannel = 'database'
    ) {
        $this->modelClass = $modelClass;
        $this->filters = $filters;
        $this->filename = $filename;
        $this->userId = $userId;
        $this->notificationChannel = $notificationChannel;
        
        $this->onQueue(config('excel.queue_name', 'excel'));
    }
    
    public function handle(ExcelService $excelService): void
    {
        try {
            Log::info("Starting Excel export for model: {$this->modelClass}");
            
            $filePath = $excelService->export($this->modelClass, $this->filters, $this->filename);
            
            $this->notifySuccess($filePath);
            
            Log::info("Excel export completed successfully: {$filePath}");
            
        } catch (\Exception $e) {
            Log::error("Excel export failed: " . $e->getMessage());
            $this->notifyError($e->getMessage());
            throw $e;
        }
    }
    
    protected function notifySuccess(string $filePath): void
    {
        $filename = basename($filePath);
        $downloadUrl = route('excel.download', ['filename' => $filename]);
        
        $data = [
            'type' => 'excel_export_success',
            'message' => "Export Excel thành công: {$filename}",
            'file_path' => $filePath,
            'download_url' => $downloadUrl,
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
            'type' => 'excel_export_error',
            'message' => "Export Excel thất bại: {$error}",
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
        // This would typically use Laravel's notification system
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
        Log::error("Excel export job failed permanently", [
            'model' => $this->modelClass,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
        
        $this->notifyError($exception->getMessage());
    }
}
