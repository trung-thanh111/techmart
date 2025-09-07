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
        Schema::create('promotion_buy_product_take_gift', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('buy_product_id');
            $table->unsignedBigInteger('buy_product_variant_id');
            $table->integer('buy_quantity');
            $table->unsignedBigInteger('get_product_id');
            $table->unsignedBigInteger('get_product_variant_id');
            $table->integer('get_quantity');
            $table->boolean('is_same_product')->default(false);
            $table->timestamps();
            $table->foreign('buy_product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('buy_product_variant_id')->references('id')->on('product_variants')->onDelete('cascade');
            $table->foreign('get_product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('get_product_variant_id')->references('id')->on('product_variants')->onDelete('cascade');

            $table->index(['promotion_id', 'buy_product_id', 'buy_product_variant_id']);
        });
        DB::statement('ALTER TABLE promotion_buy_product_take_gift ADD CONSTRAINT chk_buy_exclusive CHECK (
            (buy_product_id IS NOT NULL AND buy_product_variant_id IS NULL) OR
            (buy_product_id IS NULL AND buy_product_variant_id IS NOT NULL)
        )');

        DB::statement('ALTER TABLE promotion_buy_product_take_gift ADD CONSTRAINT chk_get_exclusive CHECK (
            (get_product_id IS NOT NULL AND get_product_variant_id IS NULL) OR
            (get_product_id IS NULL AND get_product_variant_id IS NOT NULL)
        )');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion_buy_product_take_gift');
    }
};
