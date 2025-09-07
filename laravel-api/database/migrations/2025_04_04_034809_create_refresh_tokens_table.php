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
        Schema::create('refresh_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('refresh_token')->unique();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('was_used')->default(false); // kiểm tra xem token đã được sử dụng hay chưa
            $table->boolean('is_revoked')->default(false); // nếu token đã sử dụng thì tiến hành thu hồi đảm bảo bảo mật
            $table->index(['refresh_token', 'expires_at', 'is_revoked']); // đanh chỉ mục trên các cột chọn làm điều kiện đê lấy refresh-token
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refresh_tokens');
    }
};
