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
            $table->string('coupon_code')->unique()->nullable();
            $table->text('description')->nullable();
            $table->integer('promotion_catalogue_id')->default(0);
            $table->float('discount_value')->default(0);
            $table->enum('discount_type', ['percent', 'amount'])->default('amount');
            $table->float('max_discount_value')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->integer('priority')->default(0);
            $table->boolean('can_combine')->default(false);
            $table->integer('used_count')->default(0);

            $table->tinyInteger('publish')->default(0)->comment('0: draft, 1: private, 2: publish');
            $table->timestamps();

            $table->index(['promotion_catalogue_id', 'publish']);
            $table->index(['start_date', 'end_date']);
            $table->index('priority');
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
