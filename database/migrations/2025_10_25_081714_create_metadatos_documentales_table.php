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
        // Tabla para definir campos personalizados
        Schema::create('campos_metadatos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seccion_id')->nullable()->constrained('secciones_documentales')->onDelete('cascade')
                ->comment('Si es null, aplica a todas las secciones');
            $table->string('nombre', 100);
            $table->string('etiqueta', 200);
            $table->string('tipo', ['texto', 'numero', 'fecha', 'desplegable', 'checkbox', 'textarea', 'email', 'url', 'telefono']);
            $table->text('descripcion')->nullable();
            $table->jsonb('opciones')->nullable()->comment('Para tipo desplegable');
            $table->boolean('requerido')->default(false);
            $table->boolean('multiple')->default(false)->comment('Para desplegables múltiples');
            $table->string('valor_defecto')->nullable();
            $table->string('placeholder')->nullable();
            $table->text('validacion')->nullable()->comment('Reglas de validación Laravel');
            $table->integer('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->boolean('buscable')->default(true);
            $table->boolean('visible_listado')->default(false);
            
            $table->foreignId('creado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['seccion_id', 'activo']);
            $table->unique(['seccion_id', 'nombre']);
        });

        // Tabla para valores de metadatos
        Schema::create('valores_metadatos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('documento_id')->constrained('documentos_gestion')->onDelete('cascade');
            $table->foreignId('campo_id')->constrained('campos_metadatos')->onDelete('cascade');
            $table->text('valor')->nullable();
            $table->timestamps();
            
            $table->index('documento_id');
            $table->index('campo_id');
            $table->unique(['documento_id', 'campo_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('valores_metadatos');
        Schema::dropIfExists('campos_metadatos');
    }
};
