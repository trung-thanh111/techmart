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
        Schema::create('promotion_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_id')->constrained('promotions')->cascadeOnDelete();

            // Thứ tự thực thi (quan trọng khi có nhiều actions)
            $table->integer('execution_order')->default(0);

            // Action definition
            $table->string('action_type', 100); // percent, fixed, free_shipping, bxgy, tiered
            $table->decimal('action_value', 10, 2)->nullable();
            $table->decimal('max_discount', 10, 2)->nullable();
            $table->decimal('min_discount', 10, 2)->nullable();

            // Áp dụng cho đối tượng nào
            $table->string('apply_to', 50)->default('order');
            // order, cart_subtotal, shipping_fee, cheapest_item, most_expensive_item, specific_products

            // Cấu hình bổ sung (dạng JSON)
            $table->json('config')->nullable();
            // Ví dụ: {"buy_qty": 2, "get_qty": 1, "tiers": [...]}

            $table->timestamps();

            $table->index(['promotion_id', 'execution_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion_actions');
    }
};
