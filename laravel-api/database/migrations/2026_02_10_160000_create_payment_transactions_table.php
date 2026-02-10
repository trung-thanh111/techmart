<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tạo bảng payment_transactions
     * Bảng này tracking chi tiết mỗi lần gọi payment gateway
     * Hỗ trợ idempotency, retry, và audit trail
     */
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_payment_id')->nullable()->constrained('order_payments')->nullOnDelete();

            // Gateway info
            $table->string('gateway', 30)->index(); // momo, vnpay, stripe...
            $table->enum('transaction_type', ['payment', 'refund', 'query'])->default('payment');

            // Mã giao dịch
            $table->string('gateway_transaction_id')->nullable()->index(); // transId từ MoMo
            $table->string('request_id')->unique(); // idempotency key — tránh duplicate

            // Số tiền
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('VND');

            // Trạng thái
            $table->enum('status', [
                'pending',      // Đang chờ user thanh toán
                'processing',   // Đang xử lý bên gateway
                'completed',    // Thanh toán thành công
                'failed',       // Thất bại
                'refunded',     // Đã hoàn tiền
                'expired',      // Hết hạn thanh toán
                'cancelled',    // User hủy
            ])->default('pending');

            // Request/Response log (đã redact sensitive data)
            $table->json('gateway_request')->nullable();
            $table->json('gateway_response')->nullable();
            $table->text('signature')->nullable();

            // Payment URL
            $table->text('pay_url')->nullable(); // URL redirect user sang gateway
            $table->text('deeplink')->nullable(); // Deep link cho mobile app
            $table->text('qr_code_url')->nullable(); // QR code URL

            // Thời gian
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expired_at')->nullable();

            // Error tracking
            $table->string('error_code', 20)->nullable();
            $table->text('error_message')->nullable();

            // Retry tracking
            $table->unsignedTinyInteger('retry_count')->default(0);
            $table->timestamp('last_retry_at')->nullable();

            // Extra data
            $table->json('metadata')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes tối ưu query
            $table->index(['order_id', 'status']);
            $table->index(['gateway', 'gateway_transaction_id']);
            $table->index(['status', 'expired_at']); // Tìm transactions hết hạn
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
