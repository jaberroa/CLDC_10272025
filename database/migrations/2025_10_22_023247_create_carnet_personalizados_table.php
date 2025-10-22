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
        Schema::create('carnet_personalizados', function (Blueprint $table) {
            $table->id();
            $table->string('miembro_id', 36);
            $table->foreign('miembro_id')->references('id')->on('miembros')->onDelete('cascade');
            $table->foreignId('template_id')->constrained('carnet_templates')->onDelete('cascade');
            $table->string('color_primario')->default('#667eea');
            $table->string('color_secundario')->default('#764ba2');
            $table->string('color_fondo')->default('#ffffff');
            $table->string('color_texto')->default('#000000');
            $table->string('fuente_familia')->default('Arial, sans-serif');
            $table->integer('tamaño_nombre')->default(18);
            $table->integer('tamaño_profesion')->default(14);
            $table->integer('tamaño_organizacion')->default(12);
            $table->boolean('nombre_negrita')->default(true);
            $table->boolean('nombre_cursiva')->default(false);
            $table->boolean('profesion_negrita')->default(false);
            $table->boolean('profesion_cursiva')->default(false);
            $table->json('datos_personalizados')->nullable(); // Datos editables por el usuario
            $table->boolean('activo')->default(true);
            $table->timestamps();
            
            $table->unique(['miembro_id', 'template_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carnet_personalizados');
    }
};