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
        // Crear índices optimizados para rendimiento
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->index('email');
                $table->index('created_at');
            });
        }

        if (Schema::hasTable('miembros')) {
            Schema::table('miembros', function (Blueprint $table) {
                $table->index('email');
                $table->index('estado');
                $table->index('created_at');
                $table->index(['estado', 'created_at']);
            });
        }

        if (Schema::hasTable('asambleas')) {
            Schema::table('asambleas', function (Blueprint $table) {
                $table->index('fecha_asamblea');
                $table->index('estado');
                $table->index(['fecha_asamblea', 'estado']);
            });
        }

        if (Schema::hasTable('directivas')) {
            Schema::table('directivas', function (Blueprint $table) {
                $table->index('estado');
                $table->index('fecha_inicio');
                $table->index(['estado', 'fecha_inicio']);
            });
        }

        // Optimizar tabla de cache
        if (Schema::hasTable('cache')) {
            Schema::table('cache', function (Blueprint $table) {
                $table->index('expiration');
            });
        }

        // Crear tabla de jobs para queue si no existe
        if (!Schema::hasTable('jobs')) {
            Schema::create('jobs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('queue')->index();
                $table->longText('payload');
                $table->unsignedTinyInteger('attempts');
                $table->unsignedInteger('reserved_at')->nullable();
                $table->unsignedInteger('available_at');
                $table->unsignedInteger('created_at');
            });
        }

        // Crear tabla de failed_jobs si no existe
        if (!Schema::hasTable('failed_jobs')) {
            Schema::create('failed_jobs', function (Blueprint $table) {
                $table->id();
                $table->string('uuid')->unique();
                $table->text('connection');
                $table->text('queue');
                $table->longText('payload');
                $table->longText('exception');
                $table->timestamp('failed_at')->useCurrent();
            });
        }

        // Crear tabla de sessions si no existe
        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No revertir índices para mantener rendimiento
    }
};
