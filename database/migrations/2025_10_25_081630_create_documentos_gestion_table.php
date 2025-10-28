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
        Schema::create('documentos_gestion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seccion_id')->constrained('secciones_documentales')->onDelete('cascade');
            $table->foreignId('carpeta_id')->constrained('carpetas_documentales')->onDelete('cascade');
            
            // Información del archivo
            $table->string('titulo', 500);
            $table->string('slug', 500);
            $table->text('descripcion')->nullable();
            $table->string('nombre_original', 500);
            $table->string('nombre_archivo', 500)->comment('Nombre único en storage');
            $table->string('ruta', 1000);
            $table->string('extension', 20);
            $table->string('tipo_mime', 100);
            $table->bigInteger('tamano_bytes');
            $table->string('hash_archivo', 64)->nullable()->comment('SHA256 para detección de duplicados');
            
            // Versionado
            $table->integer('version')->default(1);
            $table->foreignId('documento_original_id')->nullable()->constrained('documentos_gestion')->onDelete('set null')
                ->comment('Si es una versión, referencia al documento original');
            $table->boolean('es_version_actual')->default(true);
            
            // Estado y seguridad
            $table->string('estado')->default('borrador');
            $table->boolean('requiere_firma')->default(false);
            $table->boolean('firmado')->default(false);
            $table->boolean('confidencial')->default(false);
            $table->string('nivel_acceso', 20)->default('interno')->comment('publico, interno, confidencial, restringido');
            
            // Preview y procesamiento
            $table->text('ruta_preview')->nullable()->comment('Ruta a imagen de preview');
            $table->text('contenido_indexado')->nullable()->comment('Contenido extraído para búsqueda');
            $table->boolean('procesado')->default(false);
            
            // Relaciones CRM
            $table->string('entidad_tipo')->nullable()->comment('miembro, organizacion, proyecto, contrato, etc.');
            $table->bigInteger('entidad_id')->nullable();
            
            // Fechas importantes
            $table->date('fecha_documento')->nullable()->comment('Fecha del documento (no de subida)');
            $table->date('fecha_vencimiento')->nullable();
            $table->date('fecha_revision')->nullable();
            $table->date('fecha_ultimo_acceso')->nullable();
            
            // Estadísticas
            $table->integer('total_descargas')->default(0);
            $table->integer('total_visualizaciones')->default(0);
            $table->integer('total_compartidos')->default(0);
            
            // Auditoría
            $table->foreignId('subido_por')->constrained('users')->onDelete('cascade');
            $table->foreignId('aprobado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('aprobado_en')->nullable();
            $table->foreignId('actualizado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('eliminado_por')->nullable()->constrained('users')->onDelete('set null');
            
            // Índices
            $table->index('seccion_id');
            $table->index('carpeta_id');
            $table->index('slug');
            $table->index('estado');
            $table->index('hash_archivo');
            $table->index('documento_original_id');
            $table->index('es_version_actual');
            $table->index(['entidad_tipo', 'entidad_id']);
            $table->index('fecha_vencimiento');
            $table->index('fecha_revision');
            $table->fullText(['titulo', 'descripcion', 'contenido_indexado'], 'documentos_fulltext');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos_gestion');
    }
};
