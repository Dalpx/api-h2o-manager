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
        // Tabla Principal: documento_fiscal
        Schema::create('documento_fiscal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->constrained('sucursal');
            $table->string('tipo_doc')->comment('Factura, Nota de Crédito');
            $table->string('serie_correlativo');
            $table->dateTime('fecha');
            $table->foreignId('cliente_id')->constrained('cliente');
            $table->string('condiciones_pago');
            $table->decimal('subtotal', 15, 2);
            $table->decimal('iva', 15, 2);
            $table->decimal('total', 15, 2);
            $table->string('estado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documento_fiscal');
    }
};
