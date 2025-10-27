<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_elecciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
            $table->string('slug', 100)->unique();
            $table->text('descripcion')->nullable();
            $table->string('icono', 50)->default('ri-checkbox-circle-line');
            $table->string('color', 20)->default('primary');
            $table->boolean('activo')->default(true);
            $table->integer('orden')->default(0);
            $table->timestamps();
        });

        // Insertar tipos por defecto
        DB::table('tipos_elecciones')->insert([
            [
                'nombre' => 'Junta Directiva',
                'slug' => 'directiva',
                'descripcion' => 'Elección de miembros de la Junta Directiva',
                'icono' => 'ri-team-line',
                'color' => 'primary',
                'activo' => true,
                'orden' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Comisión',
                'slug' => 'comision',
                'descripcion' => 'Elección de miembros de comisiones especializadas',
                'icono' => 'ri-group-line',
                'color' => 'success',
                'activo' => true,
                'orden' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Especial',
                'slug' => 'especial',
                'descripcion' => 'Elecciones especiales o extraordinarias',
                'icono' => 'ri-star-line',
                'color' => 'warning',
                'activo' => true,
                'orden' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_elecciones');
    }
};
