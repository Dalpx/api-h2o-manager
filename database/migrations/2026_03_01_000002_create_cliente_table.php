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
        Schema::create('cliente', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_razon_social');
            $table->string('rif_ci')->unique();
            $table->string('telefono')->nullable();
            $table->text('direccion')->nullable();
            $table->string('tipo')->comment('Natural/Jurídico');
            $table->decimal('limite_credito', 15, 2)->default(0);
            $table->integer('dias_credito')->default(0);
            $table->decimal('saldo', 15, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cliente');
    }
};
