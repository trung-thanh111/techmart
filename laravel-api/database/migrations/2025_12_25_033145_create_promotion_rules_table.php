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
        Schema::create('promotion_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_id')->constrained()->cascadeOnDelete();

            // Logic group: Cho phép nhóm rules với AND/OR
            // Ví dụ: (Rule1 AND Rule2) OR (Rule3 AND Rule4)
            $table->integer('rule_group')->default(0);
            $table->string('logic_operator', 3)->default('AND'); // AND, OR

            // Rule definition
            $table->string('rule_type', 50); // min_order_total, product_qty, customer_group, time_slot
            $table->string('operator', 10);  // >=, <=, =, !=, IN, NOT_IN, BETWEEN
            $table->text('value');           // JSON: 500000, [1,2,3], {"from":9,"to":17}

            // Metadata
            $table->string('description')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['promotion_id', 'rule_group']);
            $table->index(['rule_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion_rules');
    }
};
