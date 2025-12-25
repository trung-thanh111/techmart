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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->nullable(); // Mã code để tracking
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            // Phân loại
            $table->string('type', 50)->index(); // order, product, flash_sale, bundle, bxgy
            $table->integer('priority')->default(0)->index(); // Số càng cao càng ưu tiên

            // Chính sách kết hợp
            $table->boolean('can_combine')->default(false);
            $table->json('combinable_with')->nullable(); // [1,2,3] - IDs có thể kết hợp

            // Giới hạn sử dụng
            $table->integer('usage_limit')->nullable(); // Giới hạn tổng số lần dùng
            $table->integer('usage_per_customer')->nullable(); // Giới hạn mỗi khách hàng
            $table->integer('used_count')->default(0); // Đếm số lần đã dùng

            // Thời gian
            $table->dateTime('start_date')->nullable()->index();
            $table->dateTime('end_date')->nullable()->index();

            // Trạng thái
            $table->tinyInteger('publish')->default(1)->index();
            $table->softDeletes();
            $table->timestamps();

            // Indexes quan trọng cho performance
            $table->index(['type', 'publish', 'start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
