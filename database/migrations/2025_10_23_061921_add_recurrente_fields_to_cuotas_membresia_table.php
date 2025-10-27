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
        Schema::table('cuotas_membresia', function (Blueprint $table) {
            $table->boolean('recurrente')->default(false)->after('estado');
            $table->string('frecuencia_recurrencia')->nullable()->after('recurrente'); // mensual, trimestral, anual
            $table->date('proxima_fecha_generacion')->nullable()->after('frecuencia_recurrencia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cuotas_membresia', function (Blueprint $table) {
            $table->dropColumn(['recurrente', 'frecuencia_recurrencia', 'proxima_fecha_generacion']);
        });
    }
};
