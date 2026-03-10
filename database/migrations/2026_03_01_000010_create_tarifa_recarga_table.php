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
        Schema::create('tarifa_recarga', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tamano_id')->constrained('tamano_recarga');
            $table->decimal('precio', 15, 2);
            $table->dateTime('fecha_desde');
            $table->dateTime('fecha_hasta')->nullable();
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursal');
            $table->foreignId('creado_por')->constrained('usuario');
            $table->string('audit_hash')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarifa_recarga');
    }
};
