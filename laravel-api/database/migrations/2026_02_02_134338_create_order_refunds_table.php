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
        Schema::create('order_refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained('order_payments')->nullOnDelete();

            // Mã hoàn tiền
            $table->string('refund_number', 20)->unique()->index();

            // Số tiền hoàn
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('VND');

            // Lý do
            $table->enum('reason', [
                'customer_request',
                'defective_product',
                'wrong_product',
                'not_as_described',
                'damaged',
                'other'
            ]);
            $table->text('reason_description')->nullable();

            // Trạng thái
            $table->enum('status', [
                'pending',
                'approved',
                'processing',
                'completed',
                'rejected'
            ])->default('pending');

            // Người xử lý
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            // Items được hoàn (JSON vì có thể hoàn một phần)
            $table->json('refund_items')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['order_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_refunds');
    }
};
