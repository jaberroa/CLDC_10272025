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
        Schema::create('cronogramas_directiva', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();
            $table->string('lugar')->nullable();
            $table->string('tipo_evento')->default('reunion'); // reunion, asamblea, capacitacion, eleccion, etc.
            $table->string('estado')->default('programado'); // programado, en_curso, completado, cancelado
            $table->foreignId('organo_id')->nullable()->constrained('organos')->onDelete('set null');
            $table->foreignId('responsable_id')->nullable()->constrained('miembros')->onDelete('set null');
            $table->text('observaciones')->nullable();
            $table->jsonb('participantes')->nullable(); // IDs de miembros participantes
            $table->jsonb('agenda')->nullable(); // Agenda detallada del evento
            $table->boolean('requiere_confirmacion')->default(false);
            $table->integer('cupo_maximo')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['fecha_inicio', 'estado']);
            $table->index(['tipo_evento', 'estado']);
            $table->index(['organo_id', 'fecha_inicio']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cronogramas_directiva');
    }
};
