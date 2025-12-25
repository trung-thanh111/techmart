<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('promotion_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_id')->constrained('promotions')->cascadeOnDelete();

            // Target definition
            $table->string('target_type', 100); // order, product, category, brand, customer_group, location
            $table->unsignedBigInteger('target_id')->nullable();

            // Include/Exclude logic
            $table->boolean('is_included')->default(true); // true=áp dụng, false=loại trừ

            // Metadata
            $table->json('metadata')->nullable(); // Lưu thêm thông tin như tên, slug

            $table->timestamps();

            // Indexes quan trọng
            $table->index(['promotion_id', 'target_type', 'is_included']);
            $table->index(['target_type', 'target_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE promotion_targets DROP CONSTRAINT IF EXISTS chk_target');
        Schema::dropIfExists('promotion_targets');
    }
};
