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
        Schema::create('address_orders', function (Blueprint $table) {
            $table->id();
            $table->string('title', 50)->nullable();
            $table->string('slug', 255);
            $table->string('full_name', 100);
            $table->string('phone', 20);
            $table->string('email', 150)->nullable();
            $table->string('address', 255);
            $table->string('state', 100)->nullable();
            $table->string('postal_code', 20);
            $table->string('country_code', 2)->default('VN');
            $table->boolean('is_default')->default(false);
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->tinyInteger('publish')->default(0)->comment('0: draft, 1: private, 2: publish');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('address_orders');
    }
};
