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
        Schema::create('secciones_documentales', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255);
            $table->string('slug', 255)->unique();
            $table->text('descripcion')->nullable();
            $table->string('icono', 50)->default('ri-folder-line');
            $table->string('color', 20)->default('primary');
            $table->integer('orden')->default(0);
            $table->boolean('activa')->default(true);
            $table->boolean('visible_menu')->default(true);
            
            // Permisos por defecto
            $table->jsonb('permisos_defecto')->nullable()->comment('Permisos por defecto para esta sección');
            
            // Configuración
            $table->boolean('requiere_aprobacion')->default(false);
            $table->boolean('permite_versionado')->default(true);
            $table->boolean('permite_compartir_externo')->default(false);
            $table->integer('max_tamano_archivo_mb')->default(50);
            $table->jsonb('formatos_permitidos')->nullable();
            
            // Auditoría
            $table->foreignId('creado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('actualizado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index('slug');
            $table->index('activa');
            $table->index('orden');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('secciones_documentales');
    }
};
