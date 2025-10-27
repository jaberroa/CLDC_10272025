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
        // Tabla para flujos de aprobación
        Schema::create('flujos_aprobacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seccion_id')->nullable()->constrained('secciones_documentales')->onDelete('cascade');
            $table->string('nombre', 200);
            $table->text('descripcion')->nullable();
            $table->enum('tipo', ['secuencial', 'paralelo', 'cualquiera'])->default('secuencial');
            $table->integer('min_aprobadores')->default(1);
            $table->boolean('requiere_todos')->default(false);
            $table->boolean('permite_delegar')->default(false);
            $table->integer('dias_respuesta')->default(7);
            $table->boolean('escalar_no_respuesta')->default(true);
            $table->json('escalacion_usuarios')->nullable();
            $table->boolean('activo')->default(true);
            
            $table->foreignId('creado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index('seccion_id');
            $table->index('activo');
        });

        // Tabla para aprobadores del flujo
        Schema::create('aprobadores_flujo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flujo_id')->constrained('flujos_aprobacion')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->integer('orden')->default(0);
            $table->boolean('obligatorio')->default(true);
            $table->timestamps();
            
            $table->index('flujo_id');
            $table->index(['flujo_id', 'orden']);
        });

        // Tabla para solicitudes de aprobación
        Schema::create('aprobaciones_documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('documento_id')->constrained('documentos_gestion')->onDelete('cascade');
            $table->foreignId('flujo_id')->nullable()->constrained('flujos_aprobacion')->onDelete('set null');
            $table->foreignId('aprobador_id')->constrained('users')->onDelete('cascade');
            $table->integer('orden_aprobacion')->default(0);
            
            // Estado
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado', 'delegado', 'escalado'])->default('pendiente');
            $table->text('comentarios')->nullable();
            $table->text('razon_rechazo')->nullable();
            
            // Delegación
            $table->foreignId('delegado_a')->nullable()->constrained('users')->onDelete('set null');
            $table->text('razon_delegacion')->nullable();
            
            // Fechas
            $table->timestamp('fecha_solicitud')->useCurrent();
            $table->timestamp('fecha_limite')->nullable();
            $table->timestamp('fecha_respuesta')->nullable();
            $table->timestamp('fecha_escalacion')->nullable();
            
            // Notificaciones
            $table->integer('recordatorios_enviados')->default(0);
            $table->timestamp('ultimo_recordatorio')->nullable();
            
            // Auditoría
            $table->string('ip_aprobacion')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            
            $table->index('documento_id');
            $table->index('aprobador_id');
            $table->index('estado');
            $table->index('fecha_limite');
            $table->index(['documento_id', 'orden_aprobacion']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aprobaciones_documentos');
        Schema::dropIfExists('aprobadores_flujo');
        Schema::dropIfExists('flujos_aprobacion');
    }
};
