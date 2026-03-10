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
        Schema::create('inventario_existencia', function (Blueprint $table) {
            $table->foreignId('sucursal_id')->constrained('sucursal');
            $table->foreignId('item_id')->constrained('item');
            $table->decimal('cantidad_actual', 15, 2);
            $table->primary(['sucursal_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventario_existencia');
    }
};
