<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Agregar columna estado a la tabla miembros si no existe
        if (Schema::hasTable('miembros')) {
            Schema::table('miembros', function (Blueprint $table) {
                // Verificar si la columna estado ya existe
                if (!Schema::hasColumn('miembros', 'estado')) {
                    $table->string('estado')->default('activo')->after('estado_membresia_id');
                }
            });
        }

        // Agregar columna email a la tabla miembros si no existe (para el índice)
        if (Schema::hasTable('miembros')) {
            Schema::table('miembros', function (Blueprint $table) {
                // Verificar si la columna email ya existe
                if (!Schema::hasColumn('miembros', 'email')) {
                    $table->string('email')->nullable()->after('telefono');
                }
            });
        }

        // Crear índices para la tabla miembros
        if (Schema::hasTable('miembros')) {
            Schema::table('miembros', function (Blueprint $table) {
                // Crear índices solo si no existen
                $indexes = DB::select("
                    SELECT indexname 
                    FROM pg_indexes 
                    WHERE tablename = 'miembros' 
                    AND indexname IN ('miembros_email_index', 'miembros_estado_index', 'miembros_created_at_index', 'miembros_estado_created_at_index')
                ");
                
                $existingIndexes = collect($indexes)->pluck('indexname')->toArray();
                
                if (!in_array('miembros_email_index', $existingIndexes)) {
                    $table->index('email');
                }
                
                if (!in_array('miembros_estado_index', $existingIndexes)) {
                    $table->index('estado');
                }
                
                if (!in_array('miembros_created_at_index', $existingIndexes)) {
                    $table->index('created_at');
                }
                
                if (!in_array('miembros_estado_created_at_index', $existingIndexes)) {
                    $table->index(['estado', 'created_at']);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('miembros')) {
            Schema::table('miembros', function (Blueprint $table) {
                // Eliminar índices
                $table->dropIndex(['email']);
                $table->dropIndex(['estado']);
                $table->dropIndex(['created_at']);
                $table->dropIndex(['estado', 'created_at']);
                
                // Eliminar columnas
                $table->dropColumn(['estado', 'email']);
            });
        }
    }
};