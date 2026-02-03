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
        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();

            // Trạng thái cũ và mới
            $table->string('old_status', 50)->nullable();
            $table->string('new_status', 50);

            // Ai thực hiện thay đổi
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_type', 50)->nullable(); // admin, customer

            // Ghi chú về thay đổi
            $table->text('note')->nullable();

            // Metadata bổ sung
            $table->json('metadata')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['order_id', 'created_at']);
            $table->index('new_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_status_histories');
    }
};
