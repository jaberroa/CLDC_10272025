<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DirectivaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear órganos
        $organos = [
            [
                'nombre' => 'Junta Directiva Nacional',
                'descripcion' => 'Órgano máximo de dirección de CLDCI',
                'tipo' => 'directiva',
                'nivel' => 'nacional',
                'activo' => true,
            ],
            [
                'nombre' => 'Comisión Electoral',
                'descripcion' => 'Comisión encargada de organizar elecciones',
                'tipo' => 'comision',
                'nivel' => 'nacional',
                'activo' => true,
            ],
            [
                'nombre' => 'Comité de Disciplina',
                'descripcion' => 'Comité encargado de asuntos disciplinarios',
                'tipo' => 'comite',
                'nivel' => 'nacional',
                'activo' => true,
            ]
        ];

        foreach ($organos as $organo) {
            \App\Models\Organo::create($organo);
        }

        // Crear cargos
        $cargos = [
            [
                'nombre' => 'Presidente',
                'descripcion' => 'Presidente de la organización',
                'nivel' => 'nacional',
                'activo' => true,
            ],
            [
                'nombre' => 'Vicepresidente',
                'descripcion' => 'Vicepresidente de la organización',
                'nivel' => 'nacional',
                'activo' => true,
            ],
            [
                'nombre' => 'Secretario',
                'descripcion' => 'Secretario de actas',
                'nivel' => 'nacional',
                'activo' => true,
            ],
            [
                'nombre' => 'Tesorero',
                'descripcion' => 'Tesorero de la organización',
                'nivel' => 'nacional',
                'activo' => true,
            ],
            [
                'nombre' => 'Vocal',
                'descripcion' => 'Miembro vocal',
                'nivel' => 'nacional',
                'activo' => true,
            ]
        ];

        foreach ($cargos as $cargo) {
            \App\Models\Cargo::create($cargo);
        }

        // Crear asignaciones de directivos para algunos miembros
        $miembros = \App\Models\Miembro::take(3)->get();
        $organos = \App\Models\Organo::all();
        $cargos = \App\Models\Cargo::all();

        foreach ($miembros as $index => $miembro) {
            if ($index < count($organos) && $index < count($cargos)) {
                \App\Models\MiembroDirectivo::create([
                    'miembro_id' => $miembro->id,
                    'organo_id' => $organos[$index]->id,
                    'cargo_id' => $cargos[$index]->id,
                    'fecha_inicio' => now()->subMonths(rand(1, 12)),
                    'fecha_fin' => null,
                    'estado' => 'activo',
                    'observaciones' => 'Asignación de prueba'
                ]);
            }
        }
    }
}
