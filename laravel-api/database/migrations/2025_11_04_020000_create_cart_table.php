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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('guest_id', 64)->nullable()->unique();
            $table->foreignId('merged_id')->nullable()->constrained('carts')->onDelete('set null');
            $table->string('coupon_code')->nullable();
            $table->json('meta')->nullable();
            $table->tinyInteger('publish')->default(2)->comment('0: draft, 1: private, 2: publish');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
