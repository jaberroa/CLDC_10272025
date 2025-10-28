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
        // Tabla para solicitudes de firma
        Schema::create('solicitudes_firma', function (Blueprint $table) {
            $table->id();
            $table->foreignId('documento_id')->constrained('documentos_gestion')->onDelete('cascade');
            $table->string('titulo', 500);
            $table->text('mensaje')->nullable();
            $table->string('tipo')->default('simple');
            $table->string('estado')->default('pendiente');
            $table->date('fecha_limite')->nullable();
            $table->boolean('requiere_orden')->default(false);
            $table->boolean('permite_rechazar')->default(true);
            $table->integer('total_firmantes')->default(0);
            $table->integer('firmantes_completados')->default(0);
            
            // Documento firmado final
            $table->string('documento_firmado_ruta')->nullable();
            $table->timestamp('completado_en')->nullable();
            
            $table->foreignId('creado_por')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('documento_id');
            $table->index('estado');
            $table->index('fecha_limite');
        });

        // Tabla para firmantes
        Schema::create('firmantes_documento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_id')->constrained('solicitudes_firma')->onDelete('cascade');
            
            // Firmante (puede ser usuario o externo)
            $table->foreignId('usuario_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('email')->nullable();
            $table->string('nombre', 255)->nullable();
            $table->integer('orden')->default(0);
            
            // Estado de la firma
            $table->string('estado')->default('pendiente');
            $table->text('razon_rechazo')->nullable();
            
            // Token para acceso externo
            $table->string('token', 100)->unique()->nullable();
            
            // Firma
            $table->text('firma_imagen')->nullable()->comment('Firma dibujada en base64');
            $table->string('firma_tipo', 50)->nullable()->comment('dibujada, texto, certificado');
            $table->text('certificado_digital')->nullable();
            
            // Auditoría de firma
            $table->timestamp('fecha_envio')->nullable();
            $table->timestamp('fecha_visto')->nullable();
            $table->timestamp('fecha_firma')->nullable();
            $table->string('ip_firma')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('ubicacion_geo')->nullable();
            $table->string('metodo_autenticacion')->nullable()->comment('password, otp, biometric');
            
            // Notificaciones
            $table->integer('recordatorios_enviados')->default(0);
            $table->timestamp('ultimo_recordatorio')->nullable();
            
            $table->timestamps();
            
            $table->index('solicitud_id');
            $table->index('usuario_id');
            $table->index('email');
            $table->index('token');
            $table->index('estado');
            $table->index(['solicitud_id', 'orden']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('firmantes_documento');
        Schema::dropIfExists('solicitudes_firma');
    }
};
