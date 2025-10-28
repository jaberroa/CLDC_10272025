<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Agregar campos faltantes a elecciones si no existen
        if (!Schema::hasColumn('elecciones', 'start_at')) {
            Schema::table('elecciones', function (Blueprint $table) {
                $table->timestamp('start_at')->nullable();
                $table->timestamp('end_at')->nullable();
            });
        }

        // Las tablas candidatos y votos ya existen en la migración principal
        // Solo agregar campos faltantes si no existen

        // Crear tabla de auditoría de votos si no existe
        if (!Schema::hasTable('auditoria_votos')) {
            Schema::create('auditoria_votos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voto_id')->nullable()->constrained('votos')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('eleccion_id')->constrained('elecciones');
            $table->string('accion', 50); // 'voto_registrado', 'intento_duplicado', etc.
            $table->text('detalles')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at');
            
            $table->index(['user_id', 'eleccion_id', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('auditoria_votos');
        
        if (Schema::hasColumn('elecciones', 'start_at')) {
            Schema::table('elecciones', function (Blueprint $table) {
                $table->dropColumn(['start_at', 'end_at']);
            });
        }
    }
};
