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
        Schema::create('item', function (Blueprint $table) {
            $table->id();
            $table->string('tipo')->comment('PRODUCTO, SERVICIO, INSUMO');
            $table->string('nombre');
            $table->string('sku')->unique();
            $table->string('unidad_medida');
            $table->boolean('grava_iva')->default(true);
            $table->foreignId('proveedor_id')->constrained('proveedor');
            $table->foreignId('cuenta_contable_venta_id')->constrained('cuenta_contable');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item');
    }
};
