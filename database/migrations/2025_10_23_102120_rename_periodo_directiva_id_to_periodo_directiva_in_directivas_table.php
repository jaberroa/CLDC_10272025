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
        // Verificar si la tabla existe antes de modificarla
        if (Schema::hasTable('directivas')) {
            Schema::table('directivas', function (Blueprint $table) {
                // Verificar si existe la clave foránea antes de eliminarla
                if (Schema::hasColumn('directivas', 'periodo_directiva_id')) {
                    $table->dropColumn('periodo_directiva_id');
                }
                $table->string('periodo_directiva')->nullable()->after('cargo_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Verificar si la tabla existe antes de modificarla
        if (Schema::hasTable('directivas')) {
            Schema::table('directivas', function (Blueprint $table) {
                $table->dropColumn('periodo_directiva');
                $table->foreignId('periodo_directiva_id')->nullable()->constrained('periodos_directiva')->onDelete('set null');
            });
        }
    }
};