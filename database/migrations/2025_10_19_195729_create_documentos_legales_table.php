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
        Schema::create('documentos_legales', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organizacion_id')->nullable();
            $table->string('tipo'); // 'estatuto', 'reglamento', 'acta', 'resolucion', 'circular'
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('numero_documento')->nullable();
            $table->date('fecha_emision');
            $table->date('fecha_vigencia')->nullable();
            $table->string('archivo_url');
            $table->boolean('activo')->default(true);
            $table->uuid('created_by')->nullable();
            $table->timestamps();

            $table->foreign('organizacion_id')->references('id')->on('organizaciones')->onDelete('cascade');
            $table->index(['organizacion_id', 'tipo', 'activo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos_legales');
    }
};