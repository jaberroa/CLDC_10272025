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
            $table->uuid('created_by')->nullable();
            $table->timestamps();

            $table->foreign('organizacion_id')->references('id')->on('organizaciones')->onDelete('cascade');
            $table->index(['organizacion_id', 'fecha_inicio']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capacitaciones');
    }
};