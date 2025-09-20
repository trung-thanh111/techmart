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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->enum('type', ['homepage', 'category', 'flash_sale', 'popup', 'custom'])->default('homepage');
            $table->enum('position', ['main_slider', 'homepage_top_ads', 'homepage_middle_ads', 'homepage_bottom_ads', 'category_top_banner', 'category_sidebar_banner', 'category_bottom_banner', 'flash_sale_header', 'flash_sale_footer', 'popup_center', 'popup_bottom_right', 'custom_html', 'custom_slot'])->default('custom_slot');
            $table->timestamp('start_at')->nullable();
            $table->timestamp('end_at')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_hot')->default(false);
            $table->string('link_url', 1000)->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->tinyInteger('publish')->default(0)->comment('0: draft, 1: private, 2: publish');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'publish', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
