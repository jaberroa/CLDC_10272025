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

        // Crear tabla de candidatos si no existe
        if (!Schema::hasTable('candidatos')) {
            Schema::create('candidatos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eleccion_id')->constrained('elecciones')->onDelete('cascade');
            $table->string('nombre', 255);
            $table->string('cargo', 100);
            $table->text('biografia')->nullable();
            $table->text('propuestas')->nullable(); // JSON
            $table->string('foto')->nullable();
            $table->integer('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
            
            $table->index(['eleccion_id', 'cargo']);
            });
        }

        // Crear tabla de votos si no existe
        if (!Schema::hasTable('votos')) {
            Schema::create('votos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('eleccion_id')->constrained('elecciones')->onDelete('cascade');
            $table->foreignId('candidato_id')->constrained('candidatos')->onDelete('cascade');
            $table->string('hash', 64)->unique()->comment('SHA-256 hash para auditoría');
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at');
            
            // Un usuario solo puede votar una vez por elección
            $table->unique(['user_id', 'eleccion_id'], 'unique_user_election_vote');
            
            $table->index(['eleccion_id', 'candidato_id']);
            $table->index('created_at');
            });
        }

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
        Schema::dropIfExists('votos');
        Schema::dropIfExists('candidatos');
        
        if (Schema::hasColumn('elecciones', 'start_at')) {
            Schema::table('elecciones', function (Blueprint $table) {
                $table->dropColumn(['start_at', 'end_at']);
            });
        }
    }
};
