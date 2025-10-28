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
        // Tabla para roles documentales
        Schema::create('roles_documentales', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('slug', 100)->unique();
            $table->text('descripcion')->nullable();
            $table->jsonb('permisos')->comment('Array de permisos: ver, crear, editar, eliminar, compartir, aprobar, firmar, etc.');
            $table->integer('nivel_acceso')->default(1)->comment('1=básico, 2=medio, 3=alto, 4=admin');
            $table->boolean('es_sistema')->default(false)->comment('No se puede eliminar');
            $table->boolean('activo')->default(true);
            
            $table->foreignId('creado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index('slug');
            $table->index('activo');
        });

        // Tabla para asignación de permisos a usuarios
        Schema::create('permisos_usuarios_documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('rol_id')->nullable()->constrained('roles_documentales')->onDelete('cascade');
            
            // Ámbito del permiso (puede ser global, sección, carpeta o documento específico)
            $table->string('ambito')->default('global')->comment('global, seccion, carpeta, documento');
            $table->foreignId('seccion_id')->nullable()->constrained('secciones_documentales')->onDelete('cascade');
            $table->foreignId('carpeta_id')->nullable()->constrained('carpetas_documentales')->onDelete('cascade');
            $table->foreignId('documento_id')->nullable()->constrained('documentos_gestion')->onDelete('cascade');
            
            // Permisos específicos (si no usa rol)
            $table->jsonb('permisos_personalizados')->nullable();
            
            // Vigencia
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->boolean('activo')->default(true);
            
            $table->foreignId('asignado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index('usuario_id');
            $table->index('rol_id');
            $table->index('ambito');
            $table->index('seccion_id');
            $table->index('carpeta_id');
            $table->index('documento_id');
            $table->index('activo');
            $table->index(['usuario_id', 'ambito']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permisos_usuarios_documentos');
        Schema::dropIfExists('roles_documentales');
    }
};
