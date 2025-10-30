<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuotas_historial', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cuota_id');
            $table->string('estado_anterior')->nullable();
            $table->string('estado_nuevo');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('motivo')->nullable();
            $table->timestamps();

            $table->foreign('cuota_id')->references('id')->on('cuotas_membresia')->onDelete('cascade');
            $table->index(['cuota_id', 'estado_nuevo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuotas_historial');
    }
};


