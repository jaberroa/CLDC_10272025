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
            $table->id();
            $table->string('miembro_id', 36);
            $table->foreignId('asamblea_id')->constrained('asambleas')->onDelete('cascade');
            $table->boolean('presente')->default(false);
            $table->timestamp('hora_llegada')->nullable();
            $table->timestamp('hora_salida')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
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
