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
        Schema::create('organizaciones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nombre');
            $table->string('tipo'); // filial, seccional, delegacion, nacional
            $table->string('codigo')->unique();
            $table->string('pais')->nullable();
            $table->string('provincia')->nullable();
            $table->string('ciudad')->nullable();
            $table->text('direccion')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->uuid('organizacion_padre_id')->nullable();
            $table->date('fecha_fundacion')->nullable();
            $table->string('estado_adecuacion')->default('pendiente');
            $table->string('estatutos_url')->nullable();
            $table->string('actas_fundacion_url')->nullable();
            $table->integer('miembros_minimos')->default(15);
            $table->timestamps();

            $table->foreign('organizacion_padre_id')->references('id')->on('organizaciones')->onDelete('cascade');
            $table->index(['tipo', 'pais']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizaciones');
    }
};