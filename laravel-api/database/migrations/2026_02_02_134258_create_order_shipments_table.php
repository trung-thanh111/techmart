<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();

            // Thông tin vận chuyển
            $table->string('tracking_number')->unique()->index();
            $table->string('carrier', 100); // Đơn vị vận chuyển
            $table->string('service_type', 50)->nullable(); // standard, express

            // Trạng thái
            $table->enum('status', [
                'pending',
                'picked_up',
                'in_transit',
                'out_for_delivery',
                'delivered',
                'failed',
                'returned'
            ])->default('pending');

            // Thông tin gói hàng
            $table->decimal('weight', 10, 2)->nullable(); // kg
            $table->json('dimensions')->nullable(); // {length, width, height}
            $table->integer('package_count')->default(1);

            // Phí vận chuyển
            $table->decimal('shipping_cost', 15, 2)->default(0);

            // Thời gian dự kiến và thực tế
            $table->timestamp('estimated_delivery_at')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('delivered_at')->nullable();

            // Người nhận
            $table->string('receiver_name')->nullable();
            $table->text('delivery_note')->nullable();

            // Metadata từ API vận chuyển
            $table->json('tracking_data')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['order_id', 'status']);
            $table->index('carrier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_shipments');
    }
};
