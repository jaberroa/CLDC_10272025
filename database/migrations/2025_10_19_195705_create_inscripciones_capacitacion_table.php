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
            $table->uuid('id')->primary();
            $table->uuid('capacitacion_id');
            $table->uuid('miembro_id');
            $table->timestamp('fecha_inscripcion')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->boolean('asistio')->default(false);
            $table->decimal('calificacion', 3, 1)->nullable();
            $table->string('certificado_url')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('capacitacion_id')->references('id')->on('capacitaciones')->onDelete('cascade');
            $table->foreign('miembro_id')->references('id')->on('miembros')->onDelete('cascade');
            $table->unique(['capacitacion_id', 'miembro_id']);
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