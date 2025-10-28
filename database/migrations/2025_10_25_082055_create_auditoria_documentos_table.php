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
        Schema::create('auditoria_documentos', function (Blueprint $table) {
            $table->id();
            
            // Relaciones
            $table->foreignId('documento_id')->nullable()->constrained('documentos_gestion')->onDelete('cascade');
            $table->foreignId('carpeta_id')->nullable()->constrained('carpetas_documentales')->onDelete('cascade');
            $table->foreignId('seccion_id')->nullable()->constrained('secciones_documentales')->onDelete('cascade');
            
            // Usuario que realiza la acción
            $table->foreignId('usuario_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('email_usuario')->nullable()->comment('Para usuarios externos');
            $table->string('nombre_usuario')->nullable();
            
            // Acción realizada
            $table->string('accion', 100)->comment('crear, ver, editar, eliminar, descargar, compartir, mover, aprobar, firmar, etc.');
            $table->string('entidad_tipo', 50)->comment('documento, carpeta, seccion, comparticion, etc.');
            $table->bigInteger('entidad_id')->nullable();
            
            // Detalles de la acción
            $table->text('descripcion')->nullable();
            $table->jsonb('datos_anteriores')->nullable();
            $table->jsonb('datos_nuevos')->nullable();
            $table->jsonb('metadatos')->nullable()->comment('Información adicional contextual');
            
            // Información de la sesión
            $table->string('ip', 45);
            $table->text('user_agent')->nullable();
            $table->string('ubicacion_geo')->nullable();
            $table->string('dispositivo')->nullable();
            $table->string('navegador')->nullable();
            
            // Resultado
            $table->string('resultado', ['exito', 'error', 'bloqueado'])->default('exito');
            $table->text('mensaje_error')->nullable();
            
            // Clasificación
            $table->string('nivel', ['info', 'warning', 'critical'])->default('info');
            $table->boolean('sospechosa')->default(false)->comment('Actividad potencialmente sospechosa');
            
            $table->timestamp('fecha_accion')->useCurrent();
            
            // Índices para búsqueda rápida
            $table->index('documento_id');
            $table->index('carpeta_id');
            $table->index('seccion_id');
            $table->index('usuario_id');
            $table->index('accion');
            $table->index(['entidad_tipo', 'entidad_id']);
            $table->index('fecha_accion');
            $table->index('ip');
            $table->index('resultado');
            $table->index('sospechosa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditoria_documentos');
    }
};
