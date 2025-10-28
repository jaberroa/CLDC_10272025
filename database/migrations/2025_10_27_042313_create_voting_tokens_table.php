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
        Schema::create('voting_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique(); // Token único para el link
            $table->bigInteger('eleccion_id');
            $table->string('tipo', 20); // 'publico' o 'privado'
            $table->string('cedula_votante', 20)->nullable(); // Para bloquear por usuario
            $table->string('ip_address', 45)->nullable(); // Para bloquear por IP
            $table->boolean('usado')->default(false); // Si ya fue usado
            $table->timestamp('fecha_expiracion'); // Fecha de expiración
            $table->timestamp('fecha_uso')->nullable(); // Cuándo se usó
            $table->timestamps();
            
            $table->foreign('eleccion_id')->references('id')->on('elecciones')->onDelete('cascade');
            $table->index(['token', 'usado']);
            $table->index(['cedula_votante', 'eleccion_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voting_tokens');
    }
};
