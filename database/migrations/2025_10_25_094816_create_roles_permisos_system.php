<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla de Roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
            $table->string('slug', 100)->unique();
            $table->string('descripcion')->nullable();
            $table->string('color', 20)->default('#0d6efd');
            $table->integer('nivel')->default(0); // Nivel jerárquico
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Tabla de Permisos
        Schema::create('permisos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('slug', 100)->unique();
            $table->string('descripcion')->nullable();
            $table->string('modulo', 50); // miembros, documentos, finanzas, etc.
            $table->string('categoria', 50)->nullable(); // CRUD, gestión, reportes
            $table->timestamps();
        });

        // Tabla Pivote: Roles - Permisos
        Schema::create('rol_permiso', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rol_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('permiso_id')->constrained('permisos')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['rol_id', 'permiso_id']);
        });

        // Tabla: Usuarios - Roles (Relación Muchos a Muchos)
        Schema::create('usuario_rol', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('rol_id')->constrained('roles')->onDelete('cascade');
            $table->timestamp('asignado_en')->useCurrent();
            $table->foreignId('asignado_por')->nullable()->constrained('users');
            $table->timestamps();
            
            $table->unique(['user_id', 'rol_id']);
        });

        // Tabla: Permisos Especiales por Usuario (override)
        Schema::create('usuario_permiso', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('permiso_id')->constrained('permisos')->onDelete('cascade');
            $table->boolean('concedido')->default(true); // true = conceder, false = denegar
            $table->timestamp('valido_desde')->nullable();
            $table->timestamp('valido_hasta')->nullable();
            $table->foreignId('asignado_por')->nullable()->constrained('users');
            $table->text('motivo')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'permiso_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario_permiso');
        Schema::dropIfExists('usuario_rol');
        Schema::dropIfExists('rol_permiso');
        Schema::dropIfExists('permisos');
        Schema::dropIfExists('roles');
    }
};
