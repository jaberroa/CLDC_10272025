<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Verificar si la tabla existe antes de modificarla
        if (Schema::hasTable('elecciones')) {
            // Usar Schema Builder para compatibilidad con múltiples bases de datos
            Schema::table('elecciones', function (Blueprint $table) {
                // Cambiar el tipo de columna usando Schema Builder
                $table->string('tipo', 50)->default('junta_directiva')->change();
            });
        }
    }

    public function down(): void
    {
        // Verificar si la tabla existe antes de modificarla
        if (Schema::hasTable('elecciones')) {
            Schema::table('elecciones', function (Blueprint $table) {
                // Revertir a los valores originales
                $table->string('tipo', 50)->default('directiva')->change();
            });
        }
    }
};
