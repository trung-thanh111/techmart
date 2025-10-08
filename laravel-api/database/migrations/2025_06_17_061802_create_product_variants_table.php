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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('url')->nullable();
            $table->string('code')->nullable()-> comment('data example 1_2..');
            $table->integer('stock')->nullable();
            $table->string('sku')->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->string('barcode')->nullable();
            $table->json('attribute_data')->nullable();
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->tinyInteger('publish')->default(2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
