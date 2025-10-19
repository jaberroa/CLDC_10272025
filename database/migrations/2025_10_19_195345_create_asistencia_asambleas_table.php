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
        Schema::create('asistencia_asambleas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('asamblea_id');
            $table->uuid('miembro_id');
            $table->boolean('presente');
            $table->string('modalidad')->nullable(); // 'presencial', 'virtual'
            $table->timestamp('hora_registro')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamps();

            $table->foreign('asamblea_id')->references('id')->on('asambleas')->onDelete('cascade');
            $table->foreign('miembro_id')->references('id')->on('miembros')->onDelete('cascade');
            $table->unique(['asamblea_id', 'miembro_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencia_asambleas');
    }
};