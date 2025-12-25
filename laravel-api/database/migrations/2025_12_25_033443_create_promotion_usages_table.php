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
        Schema::create('promotion_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_id')->constrained('promotions')->cascadeOnDelete();
            // $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('users')->nullOnDelete();

            // Thông tin discount
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->decimal('order_total', 10, 2)->nullable();

            // Metadata
            $table->json('applied_rules')->nullable(); // Lưu rules nào đã match
            $table->json('applied_actions')->nullable(); // Lưu actions nào đã execute

            $table->timestamps();

            // Indexes
            $table->index(['promotion_id', 'customer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion_usages');
    }
};
