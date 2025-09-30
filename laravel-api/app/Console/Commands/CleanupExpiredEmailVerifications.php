<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Interfaces\EmailVerification\EmailVerificationServiceInterface;

class CleanupExpiredEmailVerifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:cleanup-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Xóa các token xác thực email đã hết hạn';

    private $emailVerificationService;

    public function __construct(EmailVerificationServiceInterface $emailVerificationService)
    {
        parent::__construct();
        $this->emailVerificationService = $emailVerificationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Bắt đầu dọn dẹp token xác thực email hết hạn...');

        $deletedCount = $this->emailVerificationService->cleanupExpiredTokens();

        $this->info("Đã xóa {$deletedCount} token hết hạn.");

        return Command::SUCCESS;
    }
}
