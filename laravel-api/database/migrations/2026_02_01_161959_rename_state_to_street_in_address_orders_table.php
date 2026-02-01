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
            $table->renameColumn('state', 'street');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('address_orders', function (Blueprint $table) {
            $table->renameColumn('street', 'state');
        });
    }
};
