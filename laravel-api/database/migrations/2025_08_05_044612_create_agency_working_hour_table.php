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
        Schema::create('agency_working_hours', function (Blueprint $table) {
            $table->id();
            $table->string('day_of_week')->nullable();
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->tinyInteger('publish')->default(2)->comment('0: là draft,1: private, 2: publish');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agency_working_hours');
    }
};
