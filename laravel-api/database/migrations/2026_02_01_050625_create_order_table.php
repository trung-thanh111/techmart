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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique()->index()->comment('order code');
            $table->foreignId('user_id')->nullable()->index()->constrained()->nullOnDelete();
            $table->string('customer_email')->unique();
            $table->string('customer_phone', 20)->nullable();
            $table->string('customer_name', 100);

            // Thông tin thanh toán (dùng decimal chính xác cao khi tình tiền)
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('shipping_fee', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('currency', 3)->default('VND');

            // TODO: Mã giảm giá (hold lại cần chứa có module mã giảm giá)
            // $table->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete();
            // $table->string('coupon_code', 50)->nullable();

            // Trạng thái đơn hàng
            $table->enum('status', [
                'pending',           // Chờ xử lý
                'confirmed',         // Đã xác nhận
                'processing',        // Đang xử lý
                'shipped',           // Đã giao vận chuyển
                'delivered',         // Đã giao hàng
                'cancelled',         // Đã hủy
                'refunded',          // Đã hoàn tiền
                'failed'             // Thất bại
            ])->default('pending')->index();

            // Trạng thái thanh toán
            $table->enum('payment_status', [
                'unpaid',           // Chưa thanh toán
                'pending',          // Đang xử lý
                'paid',             // Đã thanh toán
                'partially_paid',   // Thanh toán một phần
                'refunded',         // Đã hoàn tiền
                'failed'            // Thất bại
            ])->default('unpaid')->index();

            // Phương thức thanh toán
            $table->string('payment_method', 50)->nullable(); // COD, credit_card, momo, vnpay, etc.
            $table->string('payment_gateway', 50)->nullable(); // Cổng thanh toán sử dụng
            $table->string('transaction_id')->nullable()->index(); // Mã giao dịch từ cổng thanh toán

            // Thông tin giao hàng
            $table->string('shipping_method', 50)->nullable(); // standard, express, same_day
            $table->string('shipping_provider', 50)->nullable(); // Giao Hàng Nhanh, Viettel Post, etc.
            $table->string('tracking_number')->nullable()->index(); // Mã vận đơn

            // Địa chỉ giao hàng
            $table->string('shipping_address', 255);
            $table->string('shipping_city', 100)->nullable();
            $table->string('shipping_ward', 100)->nullable();
            $table->string('shipping_street', 100)->nullable();
            $table->string('shipping_country_code', 2)->default('VN');
            $table->string('shipping_postal_code', 20)->nullable();

            // Địa chỉ thanh toán (có thể khác địa chỉ giao hàng)
            $table->boolean('billing_same_as_shipping')->default(true);
            $table->text('billing_address')->nullable();
            $table->string('billing_city', 100)->nullable();
            $table->string('billing_ward', 100)->nullable();
            $table->string('billing_street', 100)->nullable();
            $table->string('billing_country', 2)->nullable();
            $table->string('billing_postal_code', 20)->nullable();

            // Ghi chú
            $table->text('customer_note')->nullable();
            $table->text('admin_note')->nullable();

            // TODO: Thông tin thiết bị và IP (cho phân tích và bảo mật)
            // $table->ipAddress('ip_address')->nullable();
            // $table->text('user_agent')->nullable();

            // Timestamps
            $table->timestamp('confirmed_at')->nullable()->index();
            $table->timestamp('shipped_at')->nullable()->index();
            $table->timestamp('delivered_at')->nullable()->index();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('paid_at')->nullable()->index();
            $table->softDeletes();
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['created_at', 'status']);
            $table->index(['payment_status', 'created_at']);
            $table->index('customer_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
