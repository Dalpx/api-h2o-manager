<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuenta_contable', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('nombre');
            $table->string('tipo')->comment('Activo, Pasivo, Patrimonio, Ingreso, Egreso');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuenta_contable');
    }
};
