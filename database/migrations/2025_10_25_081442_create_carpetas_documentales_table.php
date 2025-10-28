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
        Schema::create('carpetas_documentales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seccion_id')->constrained('secciones_documentales')->onDelete('cascade');
            $table->foreignId('carpeta_padre_id')->nullable()->constrained('carpetas_documentales')->onDelete('cascade');
            
            $table->string('nombre', 255);
            $table->string('slug', 255);
            $table->text('descripcion')->nullable();
            $table->string('ruta_completa', 1000)->nullable()->comment('Ruta jerárquica completa');
            $table->integer('nivel')->default(1)->comment('Nivel de anidación');
            $table->string('icono', 50)->default('ri-folder-line');
            $table->string('color', 20)->nullable();
            $table->integer('orden')->default(0);
            
            // Estado y visibilidad
            $table->boolean('activa')->default(true);
            $table->boolean('publica')->default(false);
            $table->boolean('solo_lectura')->default(false);
            
            // Configuración heredada o personalizada
            $table->boolean('hereda_permisos')->default(true);
            $table->jsonb('permisos_personalizados')->nullable();
            
            // Estadísticas
            $table->integer('total_documentos')->default(0);
            $table->bigInteger('tamano_total_bytes')->default(0);
            
            // Metadatos CRM
            $table->string('entidad_tipo')->nullable()->comment('miembro, organizacion, proyecto, etc.');
            $table->bigInteger('entidad_id')->nullable()->comment('ID de la entidad relacionada');
            
            // Auditoría
            $table->foreignId('creado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('actualizado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index('seccion_id');
            $table->index('carpeta_padre_id');
            $table->index('slug');
            $table->index('activa');
            $table->index('orden');
            $table->index(['entidad_tipo', 'entidad_id']);
            $table->unique(['seccion_id', 'carpeta_padre_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carpetas_documentales');
    }
};
