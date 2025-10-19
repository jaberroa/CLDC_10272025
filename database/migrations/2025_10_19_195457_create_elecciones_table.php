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
        Schema::create('elecciones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organizacion_id');
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('tipo'); // 'nacional', 'seccional', 'especial'
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->string('estado')->default('preparacion'); // 'preparacion', 'activa', 'finalizada', 'cancelada'
            $table->integer('votos_totales')->default(0);
            $table->boolean('votacion_abierta')->default(false);
            $table->uuid('created_by')->nullable();
            $table->timestamps();

            $table->foreign('organizacion_id')->references('id')->on('organizaciones')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elecciones');
    }
};