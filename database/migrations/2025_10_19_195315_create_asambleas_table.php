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
            $table->uuid('created_by')->nullable();
            $table->timestamps();

            $table->foreign('organizacion_id')->references('id')->on('organizaciones')->onDelete('cascade');
            $table->index(['organizacion_id', 'fecha_asamblea']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asambleas');
    }
};