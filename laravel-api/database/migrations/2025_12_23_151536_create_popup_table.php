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
        Schema::create('popups', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('slug')->unique();
            $table->enum('type', ['promotion', 'notification', 'confirm', 'form', 'guide'])->nullable();
            $table->longText('content')->nullable();
            $table->string('image')->nullable();
            $table->text('link')->nullable();
            $table->string('button_text')->nullable();
            $table->text('form_html')->nullable();
            $table->integer('show_delay')->default(0);
            $table->boolean('priority')->default(false);
            $table->tinyInteger('publish')->default(0)->comment('0: draft, 1: private, 2: publish');
            $table->timestamps();

            $table->index(['slug']);
            $table->index(['publish']);
            $table->index(['priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('popups');
    }
};
