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
        Schema::create('cuenta_por_cobrar', function (Blueprint $table) {
            $table->id();

            // Relación con el Cliente (Padre)
            $table->foreignId('cliente_id')
                ->constrained('cliente')
                ->onDelete('restrict');

            // Relación con la Factura o Documento de origen
            $table->foreignId('doc_id')
                ->constrained('documento_fiscal')
                ->onDelete('restrict');

            $table->dateTime('fecha');
            $table->dateTime('vencimiento');

            // Saldo actual de la deuda (Monto inicial - Abonos)
            $table->decimal('saldo', 15, 2)->default(0);

            // Estado de la cuenta (Ej: 'PENDIENTE', 'PAGADA', 'VENCIDA')
            $table->string('estado')->default('PENDIENTE');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuenta_por_cobrar');
    }
};
