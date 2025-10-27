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
        Schema::create('comparticion_documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('documento_id')->constrained('documentos_gestion')->onDelete('cascade');
            
            // Tipo de compartición
            $table->enum('tipo', ['interno', 'externo', 'publico'])->default('interno');
            
            // Compartido con (usuario interno)
            $table->foreignId('usuario_id')->nullable()->constrained('users')->onDelete('cascade');
            
            // Compartido con (externo)
            $table->string('email_externo')->nullable();
            $table->string('nombre_externo')->nullable();
            
            // Link de compartición
            $table->string('token', 100)->unique()->nullable();
            $table->string('password_hash')->nullable();
            
            // Permisos
            $table->boolean('puede_ver')->default(true);
            $table->boolean('puede_descargar')->default(true);
            $table->boolean('puede_comentar')->default(false);
            $table->boolean('puede_editar')->default(false);
            
            // Configuración
            $table->timestamp('fecha_expiracion')->nullable();
            $table->integer('max_accesos')->nullable();
            $table->integer('accesos_actuales')->default(0);
            $table->boolean('requiere_autenticacion')->default(false);
            $table->boolean('notificar_acceso')->default(false);
            
            // Estado
            $table->boolean('activa')->default(true);
            $table->timestamp('ultimo_acceso')->nullable();
            $table->string('ultima_ip')->nullable();
            
            // Auditoría
            $table->foreignId('compartido_por')->constrained('users')->onDelete('cascade');
            $table->text('mensaje')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('documento_id');
            $table->index('usuario_id');
            $table->index('token');
            $table->index('email_externo');
            $table->index('activa');
            $table->index('fecha_expiracion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comparticion_documentos');
    }
};
