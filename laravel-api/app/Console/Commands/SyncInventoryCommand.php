<?php

namespace App\Console\Commands;

use App\Models\ProductVariant;
use App\Services\Impl\Inventory\InventorySyncService;
use Illuminate\Console\Command;

class SyncInventoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:sync
                            {--all : Sync tất cả variants}
                            {--variant-ids= : Danh sách variant IDs cách nhau bởi dấu phẩy}
                            {--force : Force sync, không confirm}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Đồng bộ inventory records cho product variants với tất cả agencies';

    protected $inventorySyncService;

    public function __construct(InventorySyncService $inventorySyncService)
    {
        parent::__construct();
        $this->inventorySyncService = $inventorySyncService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Bắt đầu đồng bộ inventory...');
        $this->newLine();

        // Lấy danh sách variant IDs cần sync
        $variantIds = $this->getVariantIds();

        if (empty($variantIds)) {
            $this->error('❌ Không tìm thấy variants nào để sync!');
            return 1;
        }

        // Hiển thị thông tin
        $variantsCount = count($variantIds);
        $this->info("📦 Tìm thấy {$variantsCount} variants");

        // Xác nhận
        if (!$this->option('force')) {
            if (!$this->confirm('Bạn có muốn tiếp tục?', true)) {
                $this->warn('⚠️  Hủy bỏ đồng bộ.');
                return 0;
            }
        }

        // Bắt đầu sync với progress bar
        $this->newLine();
        $bar = $this->output->createProgressBar($variantsCount);
        $bar->start();

        $totalAffected = 0;
        $errors = [];

        // Chunk để tránh load quá nhiều vào memory
        $chunks = array_chunk($variantIds, 50);

        foreach ($chunks as $chunk) {
            try {
                $affected = $this->inventorySyncService->syncInventoryForVariants($chunk);
                $totalAffected += $affected;
                $bar->advance(count($chunk));
            } catch (\Exception $e) {
                $errors[] = [
                    'chunk' => implode(',', $chunk),
                    'error' => $e->getMessage()
                ];
                $bar->advance(count($chunk));
            }
        }

        $bar->finish();
        $this->newLine(2);

        // Hiển thị kết quả
        $this->info("✅ Đồng bộ hoàn tất!");
        $this->info("📊 Tổng số records được tạo/cập nhật: {$totalAffected}");

        if (!empty($errors)) {
            $this->newLine();
            $this->error('❌ Có lỗi xảy ra:');
            foreach ($errors as $error) {
                $this->line("  - Chunk [{$error['chunk']}]: {$error['error']}");
            }
            return 1;
        }

        return 0;
    }

    /**
     * Lấy danh sách variant IDs cần sync
     */
    protected function getVariantIds(): array
    {
        // Nếu có option --variant-ids
        if ($variantIdsInput = $this->option('variant-ids')) {
            $ids = array_map('intval', explode(',', $variantIdsInput));
            return ProductVariant::whereIn('id', $ids)->pluck('id')->toArray();
        }

        // Nếu có option --all hoặc không có gì
        if ($this->option('all') || !$this->option('variant-ids')) {
            return ProductVariant::pluck('id')->toArray();
        }

        return [];
    }
}
