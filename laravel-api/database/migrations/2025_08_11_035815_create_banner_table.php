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
            $table->string('position')->default('main_slider');
            $table->enum('device', ['all', 'desktop', 'tablet', 'mobile'])->default('all');
            $table->enum('display_mode', ['permanent', 'scheduled'])->default('scheduled'); // hiển thị cố định or có tgian
            $table->timestamp('start_at')->nullable();
            $table->timestamp('end_at')->nullable();
            $table->integer('sort_order')->default(0);
            $table->string('link_url')->nullable(); // liên kết bên ngoài
            $table->string('width')->nullable();
            $table->string('height')->nullable();
            $table->string('alt')->nullable()->default('alt for image');
            $table->string('class')->nullable();
            $table->enum('loading', ['lazy', 'eager', 'auto'])->default('lazy');
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
