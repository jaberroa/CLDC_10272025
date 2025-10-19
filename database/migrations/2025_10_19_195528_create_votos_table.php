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
        Schema::create('votos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('eleccion_id');
            $table->uuid('elector_id');
            $table->uuid('candidato_id');
            $table->string('voto_hash');
            $table->timestamp('timestamp_voto')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('modalidad')->nullable(); // 'presencial', 'virtual'
            $table->boolean('verificado')->default(false);
            $table->timestamps();

            $table->foreign('eleccion_id')->references('id')->on('elecciones')->onDelete('cascade');
            $table->foreign('elector_id')->references('id')->on('electores')->onDelete('cascade');
            $table->unique(['eleccion_id', 'elector_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votos');
    }
};