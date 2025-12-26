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
        Schema::table('promotions', function (Blueprint $table) {
            $table->string('image')->nullable()->after('description');
            $table->unsignedBigInteger('promotion_catalogue_id')->nullable()->after('priority');
            $table->foreign('promotion_catalogue_id')->references('id')->on('promotion_catalogues')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            $table->dropForeign(['promotion_catalogue_id']);
            $table->dropColumn(['image', 'promotion_catalogue_id']);
        });
    }
};
