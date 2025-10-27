<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('elecciones', function (Blueprint $table) {
            // Verificar si las columnas no existen antes de agregarlas
            if (!Schema::hasColumn('elecciones', 'slug')) {
                $table->string('slug')->nullable()->after('titulo');
            }
            
            if (!Schema::hasColumn('elecciones', 'requiere_quorum')) {
                $table->boolean('requiere_quorum')->default(false)->after('estado');
            }
            
            if (!Schema::hasColumn('elecciones', 'quorum_minimo')) {
                $table->integer('quorum_minimo')->nullable()->after('requiere_quorum');
            }
            
            if (!Schema::hasColumn('elecciones', 'votos_por_persona')) {
                $table->integer('votos_por_persona')->default(1)->after('quorum_minimo');
            }
            
            if (!Schema::hasColumn('elecciones', 'permite_abstencion')) {
                $table->boolean('permite_abstencion')->default(false)->after('votos_por_persona');
            }

            if (!Schema::hasColumn('elecciones', 'creado_por')) {
                $table->foreignId('creado_por')->nullable()->after('created_by')->constrained('users')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('elecciones', function (Blueprint $table) {
            $table->dropColumn([
                'slug',
                'requiere_quorum',
                'quorum_minimo',
                'votos_por_persona',
                'permite_abstencion',
                'creado_por',
            ]);
        });
    }
};
