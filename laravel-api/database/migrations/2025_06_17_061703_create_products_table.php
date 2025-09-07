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
            $table->string('description')->nullable();
            $table->longText('content')->nullable();
            $table->string('image')->nullable();
            $table->integer('price')->default(0);
            $table->string('made_in')->default(0);
            $table->string('icon')->nullable();
            $table->text('album')->nullable();
            $table->text('extra_album')->nullable();
            $table->integer('order')->nullable();
            $table->integer('product_catalogue_id')->default(0);
            $table->integer('brand_id')->default(0);
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('meta_title', 255)->nullable();
            $table->string('meta_keyword', 255)->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->tinyInteger('index')->default(2); // cho phép index phục vụ SEO
            $table->text('scheme')->nullable();
            $table->text('iframe')->nullable();
            $table->tinyInteger('publish')->default(2);
            $table->timestamp('published_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
            // Indexes
            $table->index('slug');
            $table->index('publish');
            $table->index('published_at');
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
