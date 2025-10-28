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
        Schema::create('recordatorios_documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('documento_id')->constrained('documentos_gestion')->onDelete('cascade');
            
            // Tipo de recordatorio
            $table->string('tipo')->default('revision');
            $table->string('titulo', 500);
            $table->text('mensaje')->nullable();
            
            // Destinatarios
            $table->jsonb('usuarios_ids')->nullable()->comment('Array de IDs de usuarios');
            $table->jsonb('emails_externos')->nullable();
            
            // Configuración de frecuencia
            $table->date('fecha_recordatorio');
            $table->string('frecuencia')->default('una_vez');
            $table->integer('dias_anticipacion')->default(0)->comment('Días antes del vencimiento');
            $table->integer('max_repeticiones')->nullable();
            $table->integer('repeticiones_enviadas')->default(0);
            
            // Escalación
            $table->boolean('escalar_sin_respuesta')->default(false);
            $table->integer('dias_escalacion')->nullable();
            $table->jsonb('usuarios_escalacion')->nullable();
            $table->boolean('escalado')->default(false);
            $table->timestamp('fecha_escalacion')->nullable();
            
            // Estado
            $table->boolean('activo')->default(true);
            $table->string('estado')->default('pendiente');
            $table->timestamp('ultimo_envio')->nullable();
            $table->timestamp('proximo_envio')->nullable();
            $table->timestamp('completado_en')->nullable();
            
            // Prioridad
            $table->string('prioridad')->default('normal');
            
            $table->foreignId('creado_por')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('documento_id');
            $table->index('fecha_recordatorio');
            $table->index('proximo_envio');
            $table->index('estado');
            $table->index('activo');
            $table->index('tipo');
        });

        // Tabla para historial de envíos de recordatorios
        Schema::create('historial_recordatorios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recordatorio_id')->constrained('recordatorios_documentos')->onDelete('cascade');
            $table->string('destinatario_email');
            $table->string('destinatario_nombre')->nullable();
            $table->string('estado')->default('enviado');
            $table->text('mensaje_error')->nullable();
            $table->timestamp('fecha_envio')->useCurrent();
            $table->timestamp('fecha_apertura')->nullable();
            $table->timestamp('fecha_click')->nullable();
            $table->string('ip_apertura')->nullable();
            
            $table->index('recordatorio_id');
            $table->index('destinatario_email');
            $table->index('estado');
            $table->index('fecha_envio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_recordatorios');
        Schema::dropIfExists('recordatorios_documentos');
    }
};
