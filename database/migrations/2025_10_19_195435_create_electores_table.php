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
        Schema::create('electores', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('padron_id');
            $table->uuid('miembro_id');
            $table->boolean('elegible')->default(true);
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('padron_id')->references('id')->on('padrones_electorales')->onDelete('cascade');
            $table->foreign('miembro_id')->references('id')->on('miembros')->onDelete('cascade');
            $table->unique(['padron_id', 'miembro_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('electores');
    }
};