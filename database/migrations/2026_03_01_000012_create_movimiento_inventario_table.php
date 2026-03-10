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
        // Tabla Principal: movimiento_inventario
        Schema::create('movimiento_inventario', function (Blueprint $table) {
            $table->id();
            $table->dateTime('fecha');
            $table->foreignId('sucursal_id')->constrained('sucursal');
            $table->string('tipo')->comment('compra, venta, ajuste, traslado, merma');
            $table->string('referencia_doc')->nullable();
            $table->foreignId('usuario_id')->constrained('usuario');
            $table->timestamps();
        });

        // Tabla Detalle: movimiento_inventario_detalle
        Schema::create('movimiento_inventario_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mov_id')->constrained('movimiento_inventario')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('item');
            $table->decimal('cantidad', 15, 2);
            $table->decimal('costo_unitario', 15, 2);
            $table->integer('signo')->comment('+ o -');
            $table->text('motivo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimiento_inventario');
        Schema::dropIfExists('movimiento_inventario_detalle');
    }
};
