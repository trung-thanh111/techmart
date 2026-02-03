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
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();

            // Thông tin giao dịch
            $table->string('transaction_id')->unique()->index();
            $table->string('payment_method', 50); // momo, vnpay, cod
            $table->string('payment_gateway', 50)->nullable();

            // Số tiền
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('VND');

            // Trạng thái
            $table->enum('status', [
                'pending',
                'processing',
                'completed',
                'failed',
                'refunded',
                'cancelled'
            ])->default('pending');

            // Thông tin thẻ (mã hóa)
            $table->string('card_brand', 20)->nullable(); // Visa, Mastercard
            $table->string('card_last4', 4)->nullable(); // 4 số cuối

            // Response từ payment gateway
            $table->json('gateway_response')->nullable();
            $table->text('failure_reason')->nullable();

            // Thời gian
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['order_id', 'status']);
            $table->index(['payment_method', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_payments');
    }
};
