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
        Schema::create('versiones_documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('documento_id')->constrained('documentos_gestion')->onDelete('cascade');
            $table->integer('numero_version');
            $table->string('nombre_archivo', 500);
            $table->string('ruta', 1000);
            $table->bigInteger('tamano_bytes');
            $table->string('hash_archivo', 64)->nullable();
            $table->text('comentario_version')->nullable();
            $table->enum('tipo_cambio', ['menor', 'mayor', 'critico'])->default('menor');
            $table->json('cambios')->nullable()->comment('Descripción detallada de cambios');
            
            // Estado
            $table->boolean('activa')->default(false);
            $table->boolean('descargable')->default(true);
            
            // Auditoría
            $table->foreignId('creado_por')->constrained('users')->onDelete('cascade');
            $table->timestamp('creado_en')->useCurrent();
            $table->softDeletes();
            
            $table->index('documento_id');
            $table->index(['documento_id', 'numero_version']);
            $table->unique(['documento_id', 'numero_version']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('versiones_documentos');
    }
};
