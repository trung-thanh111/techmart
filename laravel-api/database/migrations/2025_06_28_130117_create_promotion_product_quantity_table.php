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
        Schema::create('promotion_product_quantity', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('product_variant_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('product_catalogue_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('min_quantity')->nullable()->default(1);
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->enum('discount_type', ['amount', 'percent'])->nullable();
            $table->timestamps();
        });

        DB::statement('ALTER TABLE promotion_product_quantity ADD CONSTRAINT chk_target check (
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
        Schema::dropIfExists('promotion_product_quantity');
    }
};
