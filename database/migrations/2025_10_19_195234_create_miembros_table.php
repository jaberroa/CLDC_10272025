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
        Schema::create('miembros', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable(); // Laravel user ID
            $table->uuid('organizacion_id');
            $table->string('numero_carnet')->unique();
            $table->string('nombre_completo');
            $table->string('cedula')->nullable();
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->text('direccion')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('profesion')->nullable();
            $table->string('estado_membresia')->default('pendiente');
            $table->date('fecha_ingreso')->default(DB::raw('CURRENT_DATE'));
            $table->date('fecha_vencimiento')->nullable();
            $table->string('foto_url')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('organizacion_id')->references('id')->on('organizaciones')->onDelete('cascade');
            $table->index(['estado_membresia', 'organizacion_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('miembros');
    }
};