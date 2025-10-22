<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CapacitacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear capacitaciones de prueba
        $capacitaciones = [
            [
                'id' => \Illuminate\Support\Str::uuid(),
                'titulo' => 'Liderazgo Comunitario',
                'descripcion' => 'Capacitación en técnicas de liderazgo y gestión comunitaria',
                'fecha_inicio' => now()->addDays(30),
                'fecha_fin' => now()->addDays(32),
                'modalidad' => 'presencial',
                'lugar' => 'Sede Nacional CLDCI',
                'cupo_maximo' => 50,
                'costo' => 0.00,
                'activo' => true,
            ],
            [
                'id' => \Illuminate\Support\Str::uuid(),
                'titulo' => 'Gestión de Proyectos',
                'descripcion' => 'Metodologías para la planificación y ejecución de proyectos comunitarios',
                'fecha_inicio' => now()->addDays(45),
                'fecha_fin' => now()->addDays(47),
                'modalidad' => 'virtual',
                'enlace_virtual' => 'https://meet.google.com/abc-defg-hij',
                'cupo_maximo' => 100,
                'costo' => 0.00,
                'activo' => true,
            ],
            [
                'id' => \Illuminate\Support\Str::uuid(),
                'titulo' => 'Comunicación Efectiva',
                'descripcion' => 'Técnicas de comunicación para líderes comunitarios',
                'fecha_inicio' => now()->addDays(60),
                'fecha_fin' => now()->addDays(62),
                'modalidad' => 'hibrida',
                'lugar' => 'Centro de Convenciones',
                'enlace_virtual' => 'https://zoom.us/j/123456789',
                'cupo_maximo' => 75,
                'costo' => 0.00,
                'activo' => true,
            ]
        ];

        foreach ($capacitaciones as $capacitacion) {
            \App\Models\Capacitacion::create($capacitacion);
        }

        // Crear inscripciones de prueba para algunos miembros
        $miembros = \App\Models\Miembro::take(5)->get();
        $capacitaciones = \App\Models\Capacitacion::all();

        foreach ($miembros as $miembro) {
            // Inscribir a 1-2 capacitaciones aleatoriamente
            $capacitacionesAleatorias = $capacitaciones->random(rand(1, 2));
            
            foreach ($capacitacionesAleatorias as $capacitacion) {
                \App\Models\InscripcionCapacitacion::create([
                    'id' => \Illuminate\Support\Str::uuid(),
                    'miembro_id' => $miembro->id,
                    'capacitacion_id' => $capacitacion->id,
                    'fecha_inscripcion' => now()->subDays(rand(1, 10)),
                    'estado' => ['inscrito', 'completado'][rand(0, 1)],
                    'observaciones' => 'Inscripción de prueba'
                ]);
            }
        }
    }
}
