<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('item', function (Blueprint $table) {
            if (! Schema::hasColumn('item', 'stock_minimo')) {
                $table->decimal('stock_minimo', 15, 2)->default(0)->after('grava_iva');
            }
            if (! Schema::hasColumn('item', 'precio_sugerido')) {
                $table->decimal('precio_sugerido', 15, 2)->default(0)->after('stock_minimo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('item', function (Blueprint $table) {
            if (Schema::hasColumn('item', 'precio_sugerido')) {
                $table->dropColumn('precio_sugerido');
            }
            if (Schema::hasColumn('item', 'stock_minimo')) {
                $table->dropColumn('stock_minimo');
            }
        });
    }
};
