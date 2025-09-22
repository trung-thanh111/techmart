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
        Schema::create('product_catalogues', function (Blueprint $table) {
            $table->id();
            $table->integer('parent_id')->default(0);
            $table->integer('lft')->nullable();
            $table->integer('rgt')->nullable();
            $table->integer('level')->default(0);
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->string('description')->nullable();
            $table->string('image')->nullable();
            $table->integer('order')->nullable();
            $table->boolean('is_hot')->default(false);
            $table->tinyInteger('publish')->default(2);
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('meta_title', 255)->nullable();
            $table->string('meta_keyword', 255)->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->softDeletes();
            $table->timestamps();
            // $table->string('icon')->nullable();
            // $table->text('scheme')->nullable();
            // $table->text('iframe')->nullable();

            // Indexes
            $table->index(['parent_id', 'lft', 'rgt', 'level']);
            $table->index('slug');
            $table->index('publish');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_catalogues');
    }
};
