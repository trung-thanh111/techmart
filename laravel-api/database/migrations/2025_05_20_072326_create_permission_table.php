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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // users:index
            $table->string('module'); // users, product, hotel, ....
            $table->integer('value'); // mã nhị phân 2^: 2^0 là read, 2^1 là create ,....
            $table->string('title'); // Tên quyền hiển thị bên ngoài FE
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->unique(['module', 'value']);
            $table->tinyInteger('publish')->default(2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
