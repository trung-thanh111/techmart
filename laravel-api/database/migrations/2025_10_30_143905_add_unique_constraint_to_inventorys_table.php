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
        Schema::table('inventorys', function (Blueprint $table) {
            $table->unique(['product_variant_id', 'agency_id'], 'inventorys_variant_agency_unique');
            $table->index('product_variant_id', 'inventorys_product_variant_id_index');
            $table->index('agency_id', 'inventorys_agency_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventorys', function (Blueprint $table) {
            $table->dropUnique('inventorys_variant_agency_unique');
            $table->dropIndex('inventorys_product_variant_id_index');
            $table->dropIndex('inventorys_agency_id_index');
        });
    }
};
