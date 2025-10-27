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
        Schema::create('comentarios_documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('documento_id')->constrained('documentos_gestion')->onDelete('cascade');
            $table->foreignId('comentario_padre_id')->nullable()->constrained('comentarios_documentos')->onDelete('cascade');
            
            // Usuario que comenta
            $table->foreignId('usuario_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('email_externo')->nullable();
            $table->string('nombre_externo')->nullable();
            
            // Contenido
            $table->text('contenido');
            $table->json('menciones')->nullable()->comment('IDs de usuarios mencionados');
            $table->json('archivos_adjuntos')->nullable();
            
            // Posición en el documento (para comentarios contextuales)
            $table->integer('pagina')->nullable();
            $table->json('coordenadas')->nullable();
            
            // Estado
            $table->boolean('resuelto')->default(false);
            $table->foreignId('resuelto_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('resuelto_en')->nullable();
            
            // Estadísticas
            $table->integer('total_respuestas')->default(0);
            $table->integer('total_likes')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('documento_id');
            $table->index('comentario_padre_id');
            $table->index('usuario_id');
            $table->index('resuelto');
            $table->index('created_at');
        });

        // Tabla para likes de comentarios
        Schema::create('likes_comentarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comentario_id')->constrained('comentarios_documentos')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['comentario_id', 'usuario_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('likes_comentarios');
        Schema::dropIfExists('comentarios_documentos');
    }
};
