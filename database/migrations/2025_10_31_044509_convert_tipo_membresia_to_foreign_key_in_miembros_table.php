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
        // Paso 1: Crear columna temporal para almacenar el ID del tipo
        Schema::table('miembros', function (Blueprint $table) {
            if (!Schema::hasColumn('miembros', 'tipo_membresia_id')) {
                $table->unsignedBigInteger('tipo_membresia_id')->nullable();
            }
        });

        // Paso 2: Migrar datos existentes: convertir nombres a IDs
        // Primero, actualizar registros que ya tienen tipos creados
        $tipos = DB::table('tipos_membresia')->get();
        foreach ($tipos as $tipo) {
            DB::table('miembros')
                ->where('tipo_membresia', $tipo->nombre)
                ->update(['tipo_membresia_id' => $tipo->id]);
        }

        // Para tipos que no existen en la tabla, crear registros por defecto si es necesario
        // o asignar el primer tipo disponible
        $primerTipo = DB::table('tipos_membresia')->first();
        if ($primerTipo) {
            DB::table('miembros')
                ->whereNull('tipo_membresia_id')
                ->whereNotNull('tipo_membresia')
                ->update(['tipo_membresia_id' => $primerTipo->id]);
        }

        // Paso 3: Hacer la columna NOT NULL después de migrar datos
        DB::statement('ALTER TABLE miembros ALTER COLUMN tipo_membresia_id SET NOT NULL');

        // Paso 4: Eliminar la columna antigua tipo_membresia (string) y renombrar (PostgreSQL requiere usar DB::statement)
        DB::statement('ALTER TABLE miembros DROP COLUMN IF EXISTS tipo_membresia');
        DB::statement('ALTER TABLE miembros RENAME COLUMN tipo_membresia_id TO tipo_membresia');

        // Paso 5: Agregar la foreign key constraint
        Schema::table('miembros', function (Blueprint $table) {
            $table->foreign('tipo_membresia')
                ->references('id')
                ->on('tipos_membresia')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('miembros', function (Blueprint $table) {
            // Eliminar foreign key
            $table->dropForeign(['tipo_membresia']);
        });

        // Renombrar de vuelta usando DB::statement (PostgreSQL)
        DB::statement('ALTER TABLE miembros RENAME COLUMN tipo_membresia TO tipo_membresia_id');

        // Convertir de vuelta a string y migrar datos
        Schema::table('miembros', function (Blueprint $table) {
            $table->string('tipo_membresia')->nullable();
        });

        // Migrar IDs de vuelta a nombres
        $tipos = DB::table('tipos_membresia')->get();
        foreach ($tipos as $tipo) {
            DB::table('miembros')
                ->where('tipo_membresia_id', $tipo->id)
                ->update(['tipo_membresia' => $tipo->nombre]);
        }

        Schema::table('miembros', function (Blueprint $table) {
            $table->dropColumn('tipo_membresia_id');
        });
    }
};
