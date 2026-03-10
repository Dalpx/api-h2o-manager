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
        Schema::create('pago', function (Blueprint $table) {
            $table->id();

            // Relación con el documento fiscal
            $table->foreignId('doc_id')
                ->constrained('documento_fiscal')
                ->onDelete('restrict');

            $table->dateTime('fecha');

            // Métodos: Efectivo, Divisa, Transferencia, PagoMovil
            $table->string('metodo');

            // Monto con precisión contable
            $table->decimal('monto', 15, 2);

            // Datos bancarios opcionales
            $table->string('referencia_bancaria')->nullable();
            $table->string('banco')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pago');
    }
};
