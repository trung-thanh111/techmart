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
        Schema::table('agencies', function (Blueprint $table) {
            $table->boolean('is_main')->default(false)->after('iframe_map')->comment('kho giao hàng online chính');
            $table->boolean('is_visible')->default(false)->after('iframe_map')->comment('display layout');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            $table->dropColumn(['is_main', 'is_visible']);
        });
    }
};
