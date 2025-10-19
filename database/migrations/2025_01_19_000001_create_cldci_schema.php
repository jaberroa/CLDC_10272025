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
        // Tabla de organizaciones (CLDCI, seccionales, etc.)
        Schema::create('organizaciones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nombre');
            $table->string('codigo')->unique();
            $table->enum('tipo', ['nacional', 'seccional', 'seccional_internacional', 'diaspora']);
            $table->string('pais')->nullable();
            $table->string('provincia')->nullable();
            $table->string('ciudad')->nullable();
            $table->text('direccion')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->enum('estado_adecuacion', ['pendiente', 'en_revision', 'aprobada', 'rechazada'])->default('pendiente');
            $table->integer('miembros_minimos')->default(15);
            $table->date('fecha_fundacion')->nullable();
            $table->uuid('organizacion_padre_id')->nullable();
            $table->timestamps();

            $table->foreign('organizacion_padre_id')->references('id')->on('organizaciones')->onDelete('cascade');
            $table->index(['tipo', 'pais']);
        });

        // Tabla de miembros
        Schema::create('miembros', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organizacion_id');
            $table->string('user_id')->nullable(); // Laravel user ID
            $table->string('nombre_completo');
            $table->string('email')->unique();
            $table->string('cedula')->nullable();
            $table->string('telefono')->nullable();
            $table->text('direccion')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('profesion');
            $table->enum('estado_membresia', ['activa', 'suspendida', 'inactiva', 'honoraria'])->default('activa');
            $table->enum('tipo_membresia', ['fundador', 'activo', 'pasivo', 'honorifico', 'estudiante', 'diaspora'])->default('activo');
            $table->date('fecha_ingreso');
            $table->string('numero_carnet')->unique();
            $table->string('foto_url')->nullable();
            $table->text('motivo_suspension')->nullable();
            $table->date('fecha_suspension')->nullable();
            $table->string('institucion_educativa')->nullable();
            $table->string('pais_residencia')->nullable();
            $table->text('reconocimiento_detalle')->nullable();
            $table->timestamps();

            $table->foreign('organizacion_id')->references('id')->on('organizaciones')->onDelete('cascade');
            $table->index(['estado_membresia', 'tipo_membresia']);
            $table->index(['organizacion_id', 'estado_membresia']);
        });

        // Tabla de roles de usuario
        Schema::create('user_roles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('user_id'); // Laravel user ID
            $table->enum('role', ['admin', 'moderador', 'miembro'])->default('miembro');
            $table->uuid('organizacion_id')->nullable();
            $table->timestamps();

            $table->foreign('organizacion_id')->references('id')->on('organizaciones')->onDelete('cascade');
            $table->unique(['user_id', 'organizacion_id']);
        });

        // Tabla de asambleas
        Schema::create('asambleas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organizacion_id');
            $table->enum('tipo', ['ordinaria', 'extraordinaria', 'especial']);
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->date('fecha_convocatoria');
            $table->date('fecha_asamblea');
            $table->integer('quorum_minimo');
            $table->string('lugar')->nullable();
            $table->enum('modalidad', ['presencial', 'virtual', 'hibrida'])->default('presencial');
            $table->string('enlace_virtual')->nullable();
            $table->enum('estado', ['convocada', 'en_curso', 'finalizada', 'cancelada'])->default('convocada');
            $table->integer('asistentes_count')->nullable();
            $table->boolean('quorum_alcanzado')->nullable();
            $table->string('convocatoria_url')->nullable();
            $table->string('acta_url')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();

            $table->foreign('organizacion_id')->references('id')->on('organizaciones')->onDelete('cascade');
            $table->index(['organizacion_id', 'estado']);
            $table->index(['fecha_asamblea', 'estado']);
        });

        // Tabla de asistencia a asambleas
        Schema::create('asistencia_asambleas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('asamblea_id');
            $table->uuid('miembro_id');
            $table->boolean('presente')->default(false);
            $table->enum('modalidad', ['presencial', 'virtual'])->nullable();
            $table->timestamp('hora_registro')->nullable();
            $table->timestamps();

            $table->foreign('asamblea_id')->references('id')->on('asambleas')->onDelete('cascade');
            $table->foreign('miembro_id')->references('id')->on('miembros')->onDelete('cascade');
            $table->unique(['asamblea_id', 'miembro_id']);
        });

        // Tabla de órganos directivos
        Schema::create('organos_cldc', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organizacion_id');
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->enum('tipo', ['direccion', 'consultivo', 'operativo', 'especializado']);
            $table->integer('nivel_jerarquico')->default(1);
            $table->uuid('organo_padre_id')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->foreign('organizacion_id')->references('id')->on('organizaciones')->onDelete('cascade');
            $table->foreign('organo_padre_id')->references('id')->on('organos_cldc')->onDelete('cascade');
        });

        // Tabla de cargos
        Schema::create('cargos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->enum('nivel', ['nacional', 'seccional', 'especializado']);
            $table->boolean('es_presidencia')->default(false);
            $table->integer('orden_prioridad')->default(1);
            $table->timestamps();
        });

        // Tabla de miembros directivos
        Schema::create('miembros_directivos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('miembro_id');
            $table->uuid('organo_id');
            $table->uuid('cargo_id');
            $table->string('periodo');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->enum('estado', ['activo', 'finalizado', 'suspendido'])->default('activo');
            $table->boolean('es_presidente')->default(false);
            $table->text('semblanza')->nullable();
            $table->string('foto_url')->nullable();
            $table->timestamps();

            $table->foreign('miembro_id')->references('id')->on('miembros')->onDelete('cascade');
            $table->foreign('organo_id')->references('id')->on('organos_cldc')->onDelete('cascade');
            $table->foreign('cargo_id')->references('id')->on('cargos')->onDelete('cascade');
            $table->index(['organo_id', 'estado']);
        });

        // Tabla de elecciones
        Schema::create('elecciones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organizacion_id');
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->enum('tipo', ['nacional', 'seccional', 'especial']);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->enum('estado', ['preparacion', 'activa', 'finalizada', 'cancelada'])->default('preparacion');
            $table->integer('votos_totales')->default(0);
            $table->boolean('votacion_abierta')->default(false);
            $table->string('created_by')->nullable();
            $table->timestamps();

            $table->foreign('organizacion_id')->references('id')->on('organizaciones')->onDelete('cascade');
            $table->index(['organizacion_id', 'estado']);
        });

        // Tabla de candidatos
        Schema::create('candidatos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('eleccion_id');
            $table->uuid('miembro_id');
            $table->uuid('cargo_id');
            $table->string('propuesta')->nullable();
            $table->text('biografia')->nullable();
            $table->string('foto_campana')->nullable();
            $table->integer('votos_recibidos')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->foreign('eleccion_id')->references('id')->on('elecciones')->onDelete('cascade');
            $table->foreign('miembro_id')->references('id')->on('miembros')->onDelete('cascade');
            $table->foreign('cargo_id')->references('id')->on('cargos')->onDelete('cascade');
            $table->unique(['eleccion_id', 'miembro_id', 'cargo_id']);
        });

        // Tabla de votos
        Schema::create('votos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('eleccion_id');
            $table->uuid('candidato_id');
            $table->uuid('votante_id'); // miembro_id
            $table->timestamp('fecha_voto');
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->foreign('eleccion_id')->references('id')->on('elecciones')->onDelete('cascade');
            $table->foreign('candidato_id')->references('id')->on('candidatos')->onDelete('cascade');
            $table->foreign('votante_id')->references('id')->on('miembros')->onDelete('cascade');
            $table->unique(['eleccion_id', 'votante_id', 'candidato_id']);
        });

        // Tabla de cursos/capacitaciones
        Schema::create('cursos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organizacion_id');
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->enum('tipo', ['profesional', 'tecnico', 'especializado']);
            $table->enum('modalidad', ['presencial', 'virtual', 'hibrida']);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->integer('capacidad_maxima');
            $table->string('lugar')->nullable();
            $table->enum('estado', ['programada', 'en_curso', 'finalizada', 'cancelada'])->default('programada');
            $table->decimal('costo', 10, 2)->default(0);
            $table->string('created_by')->nullable();
            $table->timestamps();

            $table->foreign('organizacion_id')->references('id')->on('organizaciones')->onDelete('cascade');
            $table->index(['organizacion_id', 'estado']);
        });

        // Tabla de inscripciones a cursos
        Schema::create('inscripciones_cursos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('curso_id');
            $table->uuid('miembro_id');
            $table->enum('estado', ['inscrito', 'asistio', 'completo', 'ausente'])->default('inscrito');
            $table->date('fecha_inscripcion');
            $table->decimal('monto_pagado', 10, 2)->default(0);
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('curso_id')->references('id')->on('cursos')->onDelete('cascade');
            $table->foreign('miembro_id')->references('id')->on('miembros')->onDelete('cascade');
            $table->unique(['curso_id', 'miembro_id']);
        });

        // Tabla de documentos legales
        Schema::create('documentos_legales', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organizacion_id');
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->enum('tipo', ['estatuto', 'reglamento', 'acta', 'resolucion', 'circular']);
            $table->string('numero_documento')->nullable();
            $table->date('fecha_emision');
            $table->date('fecha_vigencia')->nullable();
            $table->string('archivo_url')->nullable();
            $table->boolean('activo')->default(true);
            $table->string('created_by')->nullable();
            $table->timestamps();

            $table->foreign('organizacion_id')->references('id')->on('organizaciones')->onDelete('cascade');
            $table->index(['organizacion_id', 'tipo', 'activo']);
        });

        // Tabla de seccionales (estructura específica)
        Schema::create('seccionales', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organizacion_id');
            $table->string('nombre');
            $table->enum('tipo', ['provincial', 'internacional']);
            $table->string('pais');
            $table->string('provincia')->nullable();
            $table->string('ciudad')->nullable();
            $table->integer('miembros_count')->default(0);
            $table->date('fecha_fundacion')->nullable();
            $table->enum('estado', ['activa', 'suspendida', 'inactiva'])->default('activa');
            $table->uuid('coordinador_id')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->text('direccion')->nullable();
            $table->timestamps();

            $table->foreign('organizacion_id')->references('id')->on('organizaciones')->onDelete('cascade');
            $table->foreign('coordinador_id')->references('id')->on('miembros')->onDelete('set null');
        });

        // Tabla de submissions para registro y adecuación
        Schema::create('seccional_submissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('seccional_nombre');
            $table->text('directiva')->nullable();
            $table->string('miembros_csv_path')->nullable();
            $table->json('actas_paths')->nullable();
            $table->boolean('miembros_min_ok')->default(false);
            $table->integer('miembros_contados')->default(0);
            $table->text('observaciones')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();
        });

        // Tabla de presupuestos
        Schema::create('presupuestos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organizacion_id');
            $table->string('categoria');
            $table->string('periodo');
            $table->decimal('monto_presupuestado', 15, 2);
            $table->decimal('monto_ejecutado', 15, 2)->default(0);
            $table->boolean('activo')->default(true);
            $table->string('created_by')->nullable();
            $table->timestamps();

            $table->foreign('organizacion_id')->references('id')->on('organizaciones')->onDelete('cascade');
            $table->index(['organizacion_id', 'periodo', 'activo']);
        });

        // Tabla de padrones electorales
        Schema::create('padrones_electorales', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organizacion_id');
            $table->string('periodo');
            $table->text('descripcion')->nullable();
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->boolean('activo')->default(true);
            $table->string('created_by')->nullable();
            $table->timestamps();

            $table->foreign('organizacion_id')->references('id')->on('organizaciones')->onDelete('cascade');
            $table->index(['organizacion_id', 'activo']);
        });

        // Tabla de auditoría de acceso a datos sensibles
        Schema::create('sensitive_data_access_audit', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('accessing_user_id');
            $table->uuid('accessed_member_id');
            $table->string('access_type');
            $table->json('accessed_fields');
            $table->text('justification')->nullable();
            $table->timestamp('created_at');

            $table->foreign('accessed_member_id')->references('id')->on('miembros')->onDelete('cascade');
            $table->index(['accessing_user_id', 'created_at']);
        });

        // Tabla de log de acceso a miembros
        Schema::create('member_access_log', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('accessing_user_id');
            $table->uuid('accessed_member_id');
            $table->string('access_type');
            $table->string('user_role');
            $table->uuid('organization_context');
            $table->timestamp('created_at');

            $table->foreign('accessed_member_id')->references('id')->on('miembros')->onDelete('cascade');
            $table->foreign('organization_context')->references('id')->on('organizaciones')->onDelete('cascade');
        });

        // Tabla de log de seguridad
        Schema::create('security_audit_log', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('user_id')->nullable();
            $table->string('action');
            $table->string('resource_type');
            $table->uuid('resource_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->boolean('success')->default(true);
            $table->text('error_message')->nullable();
            $table->json('additional_data')->nullable();
            $table->timestamp('created_at');

            $table->index(['user_id', 'created_at']);
            $table->index(['action', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_audit_log');
        Schema::dropIfExists('member_access_log');
        Schema::dropIfExists('sensitive_data_access_audit');
        Schema::dropIfExists('padrones_electorales');
        Schema::dropIfExists('presupuestos');
        Schema::dropIfExists('seccional_submissions');
        Schema::dropIfExists('seccionales');
        Schema::dropIfExists('documentos_legales');
        Schema::dropIfExists('inscripciones_cursos');
        Schema::dropIfExists('cursos');
        Schema::dropIfExists('votos');
        Schema::dropIfExists('candidatos');
        Schema::dropIfExists('elecciones');
        Schema::dropIfExists('miembros_directivos');
        Schema::dropIfExists('cargos');
        Schema::dropIfExists('organos_cldc');
        Schema::dropIfExists('asistencia_asambleas');
        Schema::dropIfExists('asambleas');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('miembros');
        Schema::dropIfExists('organizaciones');
    }
};
