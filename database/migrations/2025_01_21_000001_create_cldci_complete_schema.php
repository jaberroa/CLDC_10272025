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
        // Crear enums como tablas (MySQL no soporta enums nativos como PostgreSQL)
        
        // Tabla de tipos de organización
        Schema::create('tipos_organizacion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });

        // Tabla de estados de membresía
        Schema::create('estados_membresia', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });

        // Tabla de estados de adecuación
        Schema::create('estados_adecuacion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });

        // Tabla de roles de aplicación
        Schema::create('app_roles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });

        // Tabla de organizaciones
        Schema::create('organizaciones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nombre');
            $table->foreignId('tipo_organizacion_id')->constrained('tipos_organizacion');
            $table->string('codigo')->unique();
            $table->string('pais')->nullable();
            $table->string('provincia')->nullable();
            $table->string('ciudad')->nullable();
            $table->text('direccion')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->uuid('organizacion_padre_id')->nullable();
            $table->date('fecha_fundacion')->nullable();
            $table->foreignId('estado_adecuacion_id')->constrained('estados_adecuacion');
            $table->string('estatutos_url')->nullable();
            $table->string('actas_fundacion_url')->nullable();
            $table->integer('miembros_minimos')->default(15);
            $table->timestamps();

            $table->foreign('organizacion_padre_id')->references('id')->on('organizaciones');
        });

        // Tabla de períodos de directiva
        Schema::create('periodos_directiva', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organizacion_id');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->json('directiva'); // {presidente: "nombre", secretario: "nombre", etc}
            $table->string('acta_eleccion_url')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->foreign('organizacion_id')->references('id')->on('organizaciones');
        });

        // Tabla de miembros
        Schema::create('miembros', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->uuid('organizacion_id');
            $table->string('numero_carnet')->unique();
            $table->string('nombre_completo');
            $table->string('cedula')->nullable();
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->text('direccion')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('profesion')->nullable();
            $table->foreignId('estado_membresia_id')->constrained('estados_membresia');
            $table->date('fecha_ingreso')->default(now());
            $table->date('fecha_vencimiento')->nullable();
            $table->string('foto_url')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('organizacion_id')->references('id')->on('organizaciones');
        });

        // Tabla de asambleas
        Schema::create('asambleas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organizacion_id');
            $table->string('tipo'); // 'ordinaria', 'extraordinaria'
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->timestamp('fecha_convocatoria');
            $table->timestamp('fecha_asamblea');
            $table->string('lugar')->nullable();
            $table->string('modalidad')->default('presencial'); // 'presencial', 'virtual', 'mixta'
            $table->string('enlace_virtual')->nullable();
            $table->integer('quorum_minimo');
            $table->string('convocatoria_url')->nullable();
            $table->string('acta_url')->nullable();
            $table->string('estado')->default('convocada'); // 'convocada', 'realizada', 'cancelada'
            $table->integer('asistentes_count')->default(0);
            $table->boolean('quorum_alcanzado')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->foreign('organizacion_id')->references('id')->on('organizaciones');
        });

        // Tabla de asistencia a asambleas
        Schema::create('asistencia_asambleas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('asamblea_id');
            $table->uuid('miembro_id');
            $table->boolean('presente');
            $table->string('modalidad')->nullable(); // 'presencial', 'virtual'
            $table->timestamp('hora_registro')->useCurrent();
            $table->timestamps();

            $table->foreign('asamblea_id')->references('id')->on('asambleas');
            $table->foreign('miembro_id')->references('id')->on('miembros');
            $table->unique(['asamblea_id', 'miembro_id']);
        });

        // Tabla de padrones electorales
        Schema::create('padrones_electorales', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organizacion_id');
            $table->string('periodo');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->integer('total_electores')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->foreign('organizacion_id')->references('id')->on('organizaciones');
        });

        // Tabla de electores
        Schema::create('electores', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('padron_id');
            $table->uuid('miembro_id');
            $table->boolean('elegible')->default(true);
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('padron_id')->references('id')->on('padrones_electorales');
            $table->foreign('miembro_id')->references('id')->on('miembros');
            $table->unique(['padron_id', 'miembro_id']);
        });

        // Tabla de elecciones
        Schema::create('elecciones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('padron_id');
            $table->string('cargo');
            $table->json('candidatos'); // [{id: uuid, nombre: string, propuesta: string}]
            $table->timestamp('fecha_inicio');
            $table->timestamp('fecha_fin');
            $table->string('modalidad')->default('presencial'); // 'presencial', 'virtual', 'mixta'
            $table->string('estado')->default('programada'); // 'programada', 'activa', 'finalizada'
            $table->integer('votos_totales')->default(0);
            $table->json('resultados')->nullable();
            $table->string('auditoria_hash')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->foreign('padron_id')->references('id')->on('padrones_electorales');
        });

        // Tabla de votos
        Schema::create('votos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('eleccion_id');
            $table->uuid('elector_id');
            $table->uuid('candidato_id');
            $table->string('voto_hash');
            $table->timestamp('timestamp_voto')->useCurrent();
            $table->string('modalidad')->nullable(); // 'presencial', 'virtual'
            $table->boolean('verificado')->default(false);
            $table->timestamps();

            $table->foreign('eleccion_id')->references('id')->on('elecciones');
            $table->foreign('elector_id')->references('id')->on('electores');
            $table->unique(['eleccion_id', 'elector_id']);
        });

        // Tabla de transacciones financieras
        Schema::create('transacciones_financieras', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organizacion_id');
            $table->string('tipo'); // 'ingreso', 'gasto'
            $table->string('categoria'); // 'cuotas', 'eventos', 'patrocinios', 'operativo', etc
            $table->string('concepto');
            $table->decimal('monto', 12, 2);
            $table->date('fecha');
            $table->string('comprobante_url')->nullable();
            $table->string('metodo_pago')->nullable(); // 'efectivo', 'transferencia', 'tarjeta', etc
            $table->string('referencia')->nullable();
            $table->foreignId('aprobado_por')->nullable()->constrained('users');
            $table->text('observaciones')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->foreign('organizacion_id')->references('id')->on('organizaciones');
        });

        // Tabla de presupuestos
        Schema::create('presupuestos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organizacion_id');
            $table->string('periodo'); // '2024', '2024-Q1', etc
            $table->string('categoria');
            $table->decimal('monto_presupuestado', 12, 2);
            $table->decimal('monto_ejecutado', 12, 2)->default(0);
            $table->boolean('activo')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->foreign('organizacion_id')->references('id')->on('organizaciones');
            $table->unique(['organizacion_id', 'periodo', 'categoria']);
        });

        // Tabla de capacitaciones
        Schema::create('capacitaciones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organizacion_id')->nullable();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('tipo'); // 'curso', 'taller', 'conferencia', 'seminario'
            $table->string('modalidad')->default('presencial'); // 'presencial', 'virtual', 'mixta'
            $table->timestamp('fecha_inicio');
            $table->timestamp('fecha_fin')->nullable();
            $table->string('lugar')->nullable();
            $table->string('enlace_virtual')->nullable();
            $table->integer('capacidad_maxima')->nullable();
            $table->decimal('costo', 10, 2)->default(0);
            $table->string('certificado_template_url')->nullable();
            $table->string('estado')->default('programada'); // 'programada', 'activa', 'finalizada', 'cancelada'
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->foreign('organizacion_id')->references('id')->on('organizaciones');
        });

        // Tabla de inscripciones a capacitaciones
        Schema::create('inscripciones_capacitacion', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('capacitacion_id');
            $table->uuid('miembro_id');
            $table->timestamp('fecha_inscripcion')->useCurrent();
            $table->boolean('asistio')->default(false);
            $table->decimal('calificacion', 3, 1)->nullable();
            $table->string('certificado_url')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('capacitacion_id')->references('id')->on('capacitaciones');
            $table->foreign('miembro_id')->references('id')->on('miembros');
            $table->unique(['capacitacion_id', 'miembro_id']);
        });

        // Tabla de documentos legales
        Schema::create('documentos_legales', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organizacion_id');
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('tipo'); // 'estatuto', 'reglamento', 'resolucion', 'circular'
            $table->string('numero_documento')->nullable();
            $table->date('fecha_emision');
            $table->date('fecha_vigencia')->nullable();
            $table->string('archivo_url');
            $table->boolean('activo')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->foreign('organizacion_id')->references('id')->on('organizaciones');
        });

        // Tabla de seccional submissions
        Schema::create('seccional_submissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('seccional_nombre');
            $table->text('directiva')->nullable();
            $table->string('miembros_csv_path')->nullable();
            $table->json('actas_paths')->nullable();
            $table->boolean('miembros_min_ok')->default(false);
            $table->integer('miembros_contados')->default(0);
            $table->text('observaciones')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        // Tabla de roles de usuario
        Schema::create('user_roles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('app_role_id')->constrained('app_roles');
            $table->uuid('organizacion_id')->nullable();
            $table->timestamps();

            $table->foreign('organizacion_id')->references('id')->on('organizaciones');
            $table->unique(['user_id', 'app_role_id', 'organizacion_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('seccional_submissions');
        Schema::dropIfExists('documentos_legales');
        Schema::dropIfExists('inscripciones_capacitacion');
        Schema::dropIfExists('capacitaciones');
        Schema::dropIfExists('presupuestos');
        Schema::dropIfExists('transacciones_financieras');
        Schema::dropIfExists('votos');
        Schema::dropIfExists('elecciones');
        Schema::dropIfExists('electores');
        Schema::dropIfExists('padrones_electorales');
        Schema::dropIfExists('asistencia_asambleas');
        Schema::dropIfExists('asambleas');
        Schema::dropIfExists('miembros');
        Schema::dropIfExists('periodos_directiva');
        Schema::dropIfExists('organizaciones');
        Schema::dropIfExists('app_roles');
        Schema::dropIfExists('estados_adecuacion');
        Schema::dropIfExists('estados_membresia');
        Schema::dropIfExists('tipos_organizacion');
    }
};
