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
        Schema::create('seccional_submissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('seccional_nombre');
            $table->text('directiva')->nullable();
            $table->string('miembros_csv_path')->nullable();
            $table->json('actas_paths')->nullable();
            $table->boolean('miembros_min_ok')->default(false);
            $table->integer('miembros_contados')->default(0);
            $table->text('observaciones')->nullable();
            $table->uuid('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seccional_submissions');
    }
};