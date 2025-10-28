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
        // ========================================
        // TABLAS DE REFERENCIA Y CONFIGURACIÓN
        // ========================================
        
        // Tipos de organización
        Schema::create('tipos_organizacion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });

        // Estados de membresía
        Schema::create('estados_membresia', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('descripcion')->nullable();
            $table->string('color', 7)->default('#007bff'); // Color hex para UI
            $table->timestamps();
        });

        // Estados de adecuación
        Schema::create('estados_adecuacion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });

        // Roles de aplicación
        Schema::create('app_roles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('descripcion')->nullable();
            $table->jsonb('permisos')->nullable(); // Permisos específicos del rol
            $table->timestamps();
        });

        // ========================================
        // TABLAS PRINCIPALES
        // ========================================

        // Organizaciones
        Schema::create('organizaciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('codigo')->unique();
            $table->string('tipo')->default('seccional');
            $table->string('estado')->default('activa');
            $table->text('descripcion')->nullable();
            $table->string('direccion')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->string('logo_url')->nullable();
            $table->timestamps();
            
            $table->index(['tipo', 'estado']);
        });

        // Usuarios del sistema - Modificar tabla existente
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('miembro')->after('password');
            }
            if (!Schema::hasColumn('users', 'active')) {
                $table->boolean('active')->default(true)->after('role');
            }
        });

        // Miembros
        Schema::create('miembros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('organizacion_id')->constrained('organizaciones')->onDelete('cascade');
            $table->string('numero_carnet')->unique();
            $table->string('nombre_completo');
            $table->string('cedula')->unique();
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->text('direccion')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('profesion')->nullable();
            $table->foreignId('estado_membresia_id')->constrained('estados_membresia')->onDelete('restrict');
            $table->date('fecha_ingreso');
            $table->date('fecha_vencimiento')->nullable();
            $table->string('foto_url')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            $table->index(['organizacion_id', 'estado_membresia_id']);
            $table->index(['fecha_ingreso', 'fecha_vencimiento']);
        });

        // ========================================
        // MÓDULO CUOTAS
        // ========================================

        Schema::create('cuotas_membresia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('miembro_id')->constrained('miembros')->onDelete('cascade');
            $table->string('tipo_cuota', ['mensual', 'trimestral', 'anual']);
            $table->decimal('monto', 10, 2);
            $table->date('fecha_vencimiento');
            $table->date('fecha_pago')->nullable();
            $table->string('estado', ['pendiente', 'pagada', 'vencida', 'cancelada'])->default('pendiente');
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            $table->index(['miembro_id', 'estado']);
            $table->index(['fecha_vencimiento', 'estado']);
        });

        // ========================================
        // MÓDULO DIRECTIVA
        // ========================================

        Schema::create('organos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('tipo', ['directiva', 'comision', 'comite'])->default('directiva');
            $table->string('nivel', ['nacional', 'seccional', 'regional'])->default('nacional');
            $table->boolean('activo')->default(true);
            $table->timestamps();
            
            $table->index(['tipo', 'nivel', 'activo']);
        });

        Schema::create('cargos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('nivel', ['nacional', 'seccional', 'regional'])->default('nacional');
            $table->boolean('activo')->default(true);
            $table->timestamps();
            
            $table->index(['nivel', 'activo']);
        });

        Schema::create('miembro_directivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('miembro_id')->constrained('miembros')->onDelete('cascade');
            $table->foreignId('organo_id')->constrained('organos')->onDelete('cascade');
            $table->foreignId('cargo_id')->constrained('cargos')->onDelete('cascade');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->string('estado', ['activo', 'inactivo', 'suspendido'])->default('activo');
            $table->boolean('es_presidente')->default(false);
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            $table->index(['miembro_id', 'estado']);
            $table->index(['organo_id', 'cargo_id']);
        });

        // ========================================
        // MÓDULO ASAMBLEAS
        // ========================================

        Schema::create('asambleas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizacion_id')->constrained('organizaciones')->onDelete('cascade');
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->timestamp('fecha_convocatoria');
            $table->timestamp('fecha_asamblea');
            $table->string('lugar')->nullable();
            $table->string('tipo', ['ordinaria', 'extraordinaria', 'especial'])->default('ordinaria');
            $table->string('modalidad', ['presencial', 'virtual', 'hibrida'])->default('presencial');
            $table->string('enlace_virtual')->nullable();
            $table->integer('quorum_minimo');
            $table->string('convocatoria_url')->nullable();
            $table->string('acta_url')->nullable();
            $table->string('estado', ['convocada', 'en_proceso', 'finalizada', 'cancelada'])->default('convocada');
            $table->integer('asistentes_count')->default(0);
            $table->boolean('quorum_alcanzado')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['organizacion_id', 'estado']);
            $table->index(['fecha_asamblea', 'tipo']);
        });

        Schema::create('asistencia_asambleas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asamblea_id')->constrained('asambleas')->onDelete('cascade');
            $table->foreignId('miembro_id')->constrained('miembros')->onDelete('cascade');
            $table->boolean('presente')->default(false);
            $table->string('modalidad')->nullable();
            $table->timestamp('hora_registro')->useCurrent();
            $table->timestamps();
            
            $table->unique(['asamblea_id', 'miembro_id']);
            $table->index(['asamblea_id', 'presente']);
        });

        // ========================================
        // MÓDULO ELECCIONES
        // ========================================

        Schema::create('elecciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizacion_id')->constrained('organizaciones')->onDelete('cascade');
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->timestamp('fecha_inicio');
            $table->timestamp('fecha_fin');
            $table->string('tipo', ['directiva', 'comision', 'especial'])->default('directiva');
            $table->string('estado', ['programada', 'en_proceso', 'finalizada', 'cancelada'])->default('programada');
            $table->integer('quorum_requerido');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['organizacion_id', 'estado']);
            $table->index(['fecha_inicio', 'fecha_fin']);
        });

        Schema::create('candidatos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eleccion_id')->constrained('elecciones')->onDelete('cascade');
            $table->foreignId('miembro_id')->constrained('miembros')->onDelete('cascade');
            $table->foreignId('cargo_id')->constrained('cargos')->onDelete('cascade');
            $table->text('propuesta')->nullable();
            $table->string('estado', ['activo', 'retirado', 'descalificado'])->default('activo');
            $table->integer('votos_recibidos')->default(0);
            $table->timestamps();
            
            $table->index(['eleccion_id', 'cargo_id']);
            $table->index(['estado', 'votos_recibidos']);
        });

        Schema::create('votos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eleccion_id')->constrained('elecciones')->onDelete('cascade');
            $table->foreignId('miembro_id')->constrained('miembros')->onDelete('cascade');
            $table->foreignId('candidato_id')->constrained()->onDelete('cascade');
            $table->timestamp('fecha_voto');
            $table->timestamps();
            
            $table->unique(['eleccion_id', 'miembro_id', 'candidato_id']);
            $table->index(['eleccion_id', 'candidato_id']);
        });

        // ========================================
        // MÓDULO FORMACIÓN
        // ========================================

        Schema::create('cursos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->string('modalidad');
            $table->string('lugar')->nullable();
            $table->string('enlace_virtual')->nullable();
            $table->integer('cupo_maximo')->nullable();
            $table->decimal('costo', 10, 2)->default(0);
            $table->string('instructor')->nullable();
            $table->text('contenido')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            
            $table->index(['fecha_inicio', 'activo']);
        });

        Schema::create('inscripcion_cursos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('miembro_id')->constrained('miembros')->onDelete('cascade');
            $table->foreignId('curso_id')->constrained('cursos')->onDelete('cascade');
            $table->date('fecha_inscripcion');
            $table->string('estado', ['inscrito', 'completado', 'cancelado'])->default('inscrito');
            $table->decimal('calificacion', 5, 2)->nullable();
            $table->string('certificado_url')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            $table->index(['miembro_id', 'estado']);
            $table->index(['curso_id', 'estado']);
        });

        // ========================================
        // MÓDULO NOTICIAS
        // ========================================

        Schema::create('noticias', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('contenido');
            $table->string('tipo', ['asamblea', 'comunicado', 'capacitacion', 'eleccion', 'transaccion'])->default('comunicado');
            $table->string('estado', ['borrador', 'publicada', 'archivada'])->default('borrador');
            $table->timestamp('fecha_publicacion')->nullable();
            $table->foreignId('autor_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['tipo', 'estado']);
            $table->index(['fecha_publicacion', 'estado']);
        });

        // ========================================
        // MÓDULO CARNET
        // ========================================

        Schema::create('carnet_templates', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->longText('template_html');
            $table->longText('template_css');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('carnet_personalizados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('miembro_id')->constrained('miembros')->onDelete('cascade');
            $table->foreignId('template_id')->constrained('carnet_templates')->onDelete('cascade');
            $table->jsonb('personalizacion_json');
            $table->timestamps();
            
            $table->unique(['miembro_id', 'template_id']);
        });

        // ========================================
        // MÓDULO FINANCIERO
        // ========================================

        Schema::create('transacciones_financieras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizacion_id')->constrained('organizaciones')->onDelete('cascade');
            $table->string('tipo', ['ingreso', 'egreso'])->default('ingreso');
            $table->string('concepto');
            $table->decimal('monto', 12, 2);
            $table->date('fecha');
            $table->string('estado', ['pendiente', 'confirmada', 'cancelada'])->default('pendiente');
            $table->string('referencia')->nullable();
            $table->text('observaciones')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['organizacion_id', 'tipo', 'fecha']);
            $table->index(['estado', 'fecha']);
        });

        Schema::create('presupuestos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizacion_id')->constrained('organizaciones')->onDelete('cascade');
            $table->year('año');
            $table->string('concepto');
            $table->decimal('monto_presupuestado', 12, 2);
            $table->decimal('monto_ejecutado', 12, 2)->default(0);
            $table->timestamps();
            
            $table->index(['organizacion_id', 'año']);
        });

        // ========================================
        // MÓDULO DOCUMENTOS
        // ========================================

        Schema::create('documentos_legales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizacion_id')->constrained('organizaciones')->onDelete('cascade');
            $table->string('tipo', ['acta', 'estatuto', 'reglamento', 'resolucion', 'otro'])->default('acta');
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('archivo_url');
            $table->date('fecha_documento');
            $table->string('estado', ['borrador', 'aprobado', 'archivado'])->default('borrador');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['organizacion_id', 'tipo']);
            $table->index(['fecha_documento', 'estado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar en orden inverso para respetar foreign keys
        Schema::dropIfExists('documentos_legales');
        Schema::dropIfExists('presupuestos');
        Schema::dropIfExists('transacciones_financieras');
        Schema::dropIfExists('carnet_personalizados');
        Schema::dropIfExists('carnet_templates');
        Schema::dropIfExists('noticias');
        Schema::dropIfExists('inscripcion_cursos');
        Schema::dropIfExists('cursos');
        Schema::dropIfExists('votos');
        Schema::dropIfExists('candidatos');
        Schema::dropIfExists('elecciones');
        Schema::dropIfExists('asistencia_asambleas');
        Schema::dropIfExists('asambleas');
        Schema::dropIfExists('miembro_directivos');
        Schema::dropIfExists('cargos');
        Schema::dropIfExists('organos');
        Schema::dropIfExists('cuotas_membresia');
        Schema::dropIfExists('miembros');
        Schema::dropIfExists('organizaciones');
        Schema::dropIfExists('app_roles');
        Schema::dropIfExists('estados_adecuacion');
        Schema::dropIfExists('estados_membresia');
        Schema::dropIfExists('tipos_organizacion');
    }
};