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
        Schema::create('abono_cxc', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cxc_id')->constrained('cuenta_por_cobrar')->onDelete('cascade');
            $table->foreignId('pago_id')->constrained('pago');
            $table->decimal('monto', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abono_cxc');
    }
};
