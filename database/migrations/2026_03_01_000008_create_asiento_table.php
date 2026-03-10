<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asiento', function (Blueprint $table) {
            $table->id();
            $table->dateTime('fecha');
            $table->string('origen')->comment('venta, compra, pago, ajuste');
            $table->string('referencia')->nullable();
            $table->foreignId('sucursal_id')->constrained('sucursal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asiento_detalle');
    }
};
