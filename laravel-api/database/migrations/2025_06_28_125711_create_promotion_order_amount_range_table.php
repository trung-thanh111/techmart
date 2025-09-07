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
        // bảng này giảm giá cho đơn hàng có điều kiện
        Schema::create('promotion_order_amount_range', function (Blueprint $table) {
            $table->id();
            $table->enum('discount_type', ['amount', 'percent']);
            $table->integer('min_amount');
            $table->integer('max_amount')->nullable(); // null nghĩa là không giới hạn trên
            $table->decimal('discount_value', 10, 2);
            $table->foreignId('promotion_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion_order_amount_range');
    }
};
