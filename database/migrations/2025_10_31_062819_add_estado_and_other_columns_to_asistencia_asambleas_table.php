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
        Schema::table('asistencia_asambleas', function (Blueprint $table) {
            // Agregar columna estado si no existe
            if (!Schema::hasColumn('asistencia_asambleas', 'estado')) {
                $table->string('estado')->nullable()->after('presente');
            }
            
            // Agregar columna fecha_asistencia si no existe
            if (!Schema::hasColumn('asistencia_asambleas', 'fecha_asistencia')) {
                $table->date('fecha_asistencia')->nullable()->after('estado');
            }
            
            // Agregar columna hora_llegada si no existe (ya existe hora_registro, pero el controlador usa hora_llegada)
            if (!Schema::hasColumn('asistencia_asambleas', 'hora_llegada')) {
                $table->time('hora_llegada')->nullable()->after('fecha_asistencia');
            }
            
            // Agregar columna observaciones si no existe
            if (!Schema::hasColumn('asistencia_asambleas', 'observaciones')) {
                $table->text('observaciones')->nullable()->after('hora_llegada');
            }
            
            // Agregar columna created_by si no existe
            if (!Schema::hasColumn('asistencia_asambleas', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null')->after('observaciones');
            }
            
            // Agregar columna updated_by si no existe
            if (!Schema::hasColumn('asistencia_asambleas', 'updated_by')) {
                $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null')->after('created_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asistencia_asambleas', function (Blueprint $table) {
            // Eliminar foreign keys primero
            if (Schema::hasColumn('asistencia_asambleas', 'updated_by')) {
                $table->dropForeign(['updated_by']);
            }
            if (Schema::hasColumn('asistencia_asambleas', 'created_by')) {
                $table->dropForeign(['created_by']);
            }
            
            // Eliminar columnas
            $columnsToDrop = ['estado', 'fecha_asistencia', 'hora_llegada', 'observaciones', 'created_by', 'updated_by'];
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('asistencia_asambleas', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
