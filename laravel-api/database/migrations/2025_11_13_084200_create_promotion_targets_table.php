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
            $table->foreignId('promotion_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('product_variant_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('product_catalogue_id')->nullable()->constrained()->onDelete('cascade');
            $table->boolean('is_gift')->default(false);
            $table->integer('min_quantity')->default(1);
            $table->integer('max_quantity')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement('ALTER TABLE promotion_targets ADD CONSTRAINT chk_target check (
            (product_id IS NOT NULL AND product_variant_id IS NULL AND product_catalogue_id IS NULL) OR
            (product_id IS NULL AND product_variant_id IS NOT NULL AND product_catalogue_id IS NULL) OR
            (product_id IS NULL AND product_variant_id IS NULL AND product_catalogue_id IS NOT NULL)
        )');
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
