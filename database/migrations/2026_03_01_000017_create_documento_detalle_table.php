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
        // Tabla Detalle: documento_detalle
        Schema::create('documento_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doc_id')->constrained('documento_fiscal')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('item');
            $table->foreignId('tamano_id')->nullable()->constrained('tamano_recarga');
            $table->decimal('cantidad', 15, 2);
            $table->decimal('precio_unit', 15, 2);
            $table->decimal('iva_monto', 15, 2);
            $table->decimal('total_linea', 15, 2);
            $table->decimal('costo_estimado', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documento_detalles');
    }
};
