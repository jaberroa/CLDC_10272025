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
        Schema::create('inscripciones_capacitacion', function (Blueprint $table) {
            $table->id();
            $table->string('miembro_id', 36);
            $table->foreign('miembro_id')->references('id')->on('miembros')->onDelete('cascade');
            $table->foreignId('capacitacion_id')->constrained('capacitaciones')->onDelete('cascade');
            $table->date('fecha_inscripcion');
            $table->enum('estado', ['inscrito', 'completado', 'cancelado'])->default('inscrito');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscripciones_capacitacion');
    }
};
