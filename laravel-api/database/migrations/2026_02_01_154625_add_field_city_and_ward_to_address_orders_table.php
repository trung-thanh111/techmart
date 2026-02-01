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
        Schema::table('address_orders', function (Blueprint $table) {
            $table->string('city')->after('address')->nullable();
            $table->string('ward')->after('city')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('address_orders', function (Blueprint $table) {
            $table->dropColumn('city');
            $table->dropColumn('ward');
        });
    }
};
