<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tamano_recarga', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->comment('Ejemplo: 5L, 10L, 20L');
            $table->decimal('factor_consumo_agua', 15, 2)->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tamano_recarga');
    }
};
