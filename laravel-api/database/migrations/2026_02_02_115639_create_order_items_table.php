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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->restrictOnDelete();

            // Snapshot dữ liệu sản phẩm (quan trọng: giữ thông tin tại thời điểm mua)
            $table->string('product_sku', 100);
            $table->string('product_name');
            $table->text('product_description')->nullable();
            $table->json('product_attributes')->nullable();
            $table->string('product_image')->nullable();

            // Số lượng và giá
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 15, 2); // Giá gốc
            $table->decimal('original_price', 15, 2)->nullable(); // Giá trước khi giảm
            $table->decimal('discount_amount', 15, 2)->default(0); // Giảm giá cho item này
            $table->decimal('tax_amount', 15, 2)->default(0); // Thuế cho item này
            $table->decimal('subtotal', 15, 2); // = quantity * unit_price
            $table->decimal('total', 15, 2); // = subtotal - discount + tax

            // Thông tin bảo hành (quan trọng cho đồ công nghệ)
            $table->integer('warranty_months')->default(0);
            $table->date('warranty_expires_at')->nullable();

            // Trạng thái item (có thể khác trạng thái đơn hàng chung cho phép mỗi item có một trang thái khác nhau)
            $table->enum('status', [
                'pending',
                'confirmed',
                'processing',
                'shipped',
                'delivered',
                'returned',
                'cancelled'
            ])->default('pending');

            // Thông tin hoàn trả
            $table->boolean('is_returnable')->default(true);
            $table->integer('return_day')->default(7); // Số ngày được phép trả hàng
            $table->text('note')->nullable();

            $table->timestamps();

            $table->index(['order_id', 'product_id']);
            $table->index('product_sku');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
