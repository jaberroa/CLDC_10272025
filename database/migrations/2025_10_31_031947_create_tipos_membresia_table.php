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
        if (!Schema::hasTable('tipos_membresia')) {
            Schema::create('tipos_membresia', function (Blueprint $table) {
                $table->id();
                $table->string('nombre')->unique();
                $table->string('descripcion')->nullable();
                $table->string('color', 7)->default('#007bff'); // Color hex para UI
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipos_membresia');
    }
};
