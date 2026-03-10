<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asiento_detalle', function (Blueprint $table) {
            $table->id();

            // Llaves foráneas
            $table->foreignId('asiento_id')
                ->constrained('asiento')
                ->onDelete('cascade');

            $table->foreignId('cuenta_id')
                ->constrained('cuenta_contable');

            // Campos de montos con precisión contable
            $table->decimal('debe', 15, 2)->default(0);
            $table->decimal('haber', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asiento_detalle');
    }
};
