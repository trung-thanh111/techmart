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
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name', 100);
            $table->string('user_email', 150)->nullable();
            $table->string('user_avatar', 255)->nullable();
            $table->unsignedTinyInteger('rating')->default(5); // 1 - 5 sao
            $table->text('comment');
            $table->json('images')->nullable();
            $table->unsignedTinyInteger('publish')->default(2); // 1: Cho duyet, 2: Da xuat ban
            $table->unsignedInteger('likes_count')->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            $table->index(['product_id', 'publish']);
            $table->index(['product_id', 'rating']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
};
