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
        Schema::table('miembros', function (Blueprint $table) {
            // Eliminar índice si existe
            if (Schema::hasColumn('miembros', 'estado')) {
                // Intentar eliminar índices relacionados
                try {
                    $table->dropIndex(['estado']);
                } catch (\Exception $e) {
                    // El índice puede no existir o tener otro nombre
                }
                try {
                    $table->dropIndex(['estado', 'created_at']);
                } catch (\Exception $e) {
                    // El índice puede no existir o tener otro nombre
                }
                
                // Eliminar la columna estado (redundante con estado_membresia_id)
                $table->dropColumn('estado');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('miembros', function (Blueprint $table) {
            if (!Schema::hasColumn('miembros', 'estado')) {
                $table->string('estado')->default('activo')->after('estado_membresia_id');
                $table->index('estado');
                $table->index(['estado', 'created_at']);
            }
        });
    }
};
