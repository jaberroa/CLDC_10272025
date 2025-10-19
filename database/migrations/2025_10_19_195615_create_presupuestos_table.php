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
        Schema::create('presupuestos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organizacion_id');
            $table->string('periodo'); // '2024', '2024-Q1', etc
            $table->string('categoria');
            $table->decimal('monto_presupuestado', 12, 2);
            $table->decimal('monto_ejecutado', 12, 2)->default(0);
            $table->boolean('activo')->default(true);
            $table->uuid('created_by')->nullable();
            $table->timestamps();

            $table->foreign('organizacion_id')->references('id')->on('organizaciones')->onDelete('cascade');
            $table->unique(['organizacion_id', 'periodo', 'categoria']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presupuestos');
    }
};