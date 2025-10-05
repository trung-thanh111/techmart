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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->string('sku', 100)->unique();
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->decimal('price_origin', 15, 2)->default(0);
            $table->text('album')->nullable();
            $table->integer('sort_order')->nullable();
            $table->integer('product_catalogue_id')->default(0);
            $table->integer('faq_id')->default(0)->comment('thực chất là liên kết với nhóm lấy ra thông tin ds faq');
            $table->integer('tag_id')->default(0);
            $table->boolean('is_hot')->default(false);
            $table->integer('like_count')->default(0);
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('brand_id')->nullable()->constrained()->onDelete('set null');
            $table->string('meta_title', 255)->nullable();
            $table->string('meta_keyword', 255)->nullable();
            $table->string('meta_description', 1000)->nullable();
            $table->tinyInteger('publish')->default(2);
            $table->softDeletes();
            $table->timestamps();
            // Indexes
            $table->index(['slug', 'sku', 'price_origin']);
            $table->index('publish');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
