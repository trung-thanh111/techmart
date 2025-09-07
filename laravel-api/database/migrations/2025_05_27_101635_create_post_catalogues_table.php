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
        Schema::create('post_catalogues', function (Blueprint $table) {
            $table->id();
            $table->integer('parent_id')->default(0);
            $table->integer('lft')->nullable();
            $table->integer('rgt')->nullable();
            $table->integer('level')->default(0);
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->string('description', 500)->nullable();
            $table->longText('content')->nullable();
            $table->string('image')->nullable();
            $table->string('icon')->nullable();
            $table->text('album')->nullable();
            $table->text('extra_album')->nullable();
            $table->boolean('table_of_content')->default(false);
            $table->integer('order')->nullable();
            $table->tinyInteger('publish')->default(2);
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('meta_title', 255)->nullable();
            $table->string('meta_keyword', 255)->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->text('scheme')->nullable();
            $table->text('iframe')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
            // Indexes
            $table->index(['parent_id', 'lft', 'rgt', 'level']);
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
        Schema::dropIfExists('post_catalogues');
    }
};
