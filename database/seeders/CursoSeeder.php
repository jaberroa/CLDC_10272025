<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CursoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear cursos de prueba
        $cursos = [
            [
                'titulo' => 'Liderazgo Comunitario Avanzado',
                'descripcion' => 'Curso especializado en técnicas de liderazgo para dirigentes comunitarios',
                'fecha_inicio' => now()->addDays(15),
                'fecha_fin' => now()->addDays(17),
                'modalidad' => 'presencial',
                'lugar' => 'Centro de Capacitación CLDCI',
                'cupo_maximo' => 30,
                'costo' => 0.00,
                'instructor' => 'Dr. Juan Pérez',
                'contenido' => 'Módulo 1: Fundamentos del liderazgo\nMódulo 2: Comunicación efectiva\nMódulo 3: Resolución de conflictos',
                'activo' => true,
            ],
            [
                'titulo' => 'Gestión de Proyectos Sociales',
                'descripcion' => 'Metodologías para la planificación y ejecución de proyectos comunitarios',
                'fecha_inicio' => now()->addDays(25),
                'fecha_fin' => now()->addDays(27),
                'modalidad' => 'virtual',
                'enlace_virtual' => 'https://meet.google.com/abc-defg-hij',
                'cupo_maximo' => 50,
                'costo' => 0.00,
                'instructor' => 'Lic. María González',
                'contenido' => 'Módulo 1: Planificación de proyectos\nMódulo 2: Ejecución y seguimiento\nMódulo 3: Evaluación de resultados',
                'activo' => true,
            ],
            [
                'titulo' => 'Comunicación Digital para Líderes',
                'descripcion' => 'Herramientas digitales para la comunicación efectiva en organizaciones',
                'fecha_inicio' => now()->addDays(40),
                'fecha_fin' => now()->addDays(42),
                'modalidad' => 'hibrida',
                'lugar' => 'Sede Nacional CLDCI',
                'enlace_virtual' => 'https://zoom.us/j/123456789',
                'cupo_maximo' => 40,
                'costo' => 0.00,
                'instructor' => 'Ing. Carlos Rodríguez',
                'contenido' => 'Módulo 1: Redes sociales\nMódulo 2: Herramientas de videoconferencia\nMódulo 3: Marketing digital',
                'activo' => true,
            ]
        ];

        foreach ($cursos as $curso) {
            \App\Models\Curso::create($curso);
        }

        // Crear inscripciones de prueba para algunos miembros
        $miembros = \App\Models\Miembro::take(5)->get();
        $cursos = \App\Models\Curso::all();

        foreach ($miembros as $miembro) {
            // Inscribir a 1-2 cursos aleatoriamente
            $cursosAleatorios = $cursos->random(rand(1, 2));
            
            foreach ($cursosAleatorios as $curso) {
                \App\Models\InscripcionCurso::create([
                    'miembro_id' => $miembro->id,
                    'curso_id' => $curso->id,
                    'fecha_inscripcion' => now()->subDays(rand(1, 10)),
                    'estado' => ['inscrito', 'completado'][rand(0, 1)],
                    'calificacion' => rand(0, 1) == 1 ? rand(70, 100) : null,
                    'observaciones' => 'Inscripción de prueba'
                ]);
            }
        }
    }
}
