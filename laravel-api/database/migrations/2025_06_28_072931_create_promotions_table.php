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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('code')->unique();
            $table->integer('priority')->default(1);
            $table->decimal('combo_price', 10, 2)->nullable();
            $table->longText('description')->nullable();
            $table->boolean('is_default')->default(false);
            $table->decimal('default_discount_value', 10, 2)->nullable();
            $table->enum('default_discount_type', ['amount', 'percent'])->nullable();
            $table->decimal('default_min_quantity', 10, 2)->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->integer('usage_count')->default(0);
            $table->foreignId('promotion_catalogue_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->tinyInteger('publish')->default(2);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
