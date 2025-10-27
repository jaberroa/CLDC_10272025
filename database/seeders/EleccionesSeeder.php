<?php

namespace Database\Seeders;

use App\Models\Eleccion;
use App\Models\Candidato;
use App\Models\Organizacion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EleccionesSeeder extends Seeder
{
    public function run(): void
    {
        $organizacion = Organizacion::first();
        
        if (!$organizacion) {
            $this->command->error('No se encontró ninguna organización. Por favor, crea una primero.');
            return;
        }

        // Crear elección activa
        $eleccionActiva = Eleccion::create([
            'id' => Str::uuid(),
            'organizacion_id' => $organizacion->id,
            'titulo' => 'Elección Directiva 2024',
            'descripcion' => 'Elección de la nueva directiva para el período 2024-2026',
            'tipo' => 'directiva',
            'fecha_inicio' => now()->subDays(1),
            'fecha_fin' => now()->addDays(7),
            'start_at' => now()->subDays(1),
            'end_at' => now()->addDays(7),
            'estado' => 'activa',
            'votacion_abierta' => true,
            'created_by' => 1,
        ]);

        // Candidatos a Presidente
        Candidato::create([
            'eleccion_id' => $eleccionActiva->id,
            'nombre' => 'Juan Pérez García',
            'cargo' => 'Presidente',
            'biografia' => 'Experiencia de 6 años en la organización. Líder comprometido con el desarrollo institucional y la transparencia administrativa.',
            'propuestas' => json_encode([
                'Modernización tecnológica',
                'Transparencia administrativa',
                'Mayor participación social',
                'Fortalecimiento institucional',
            ]),
            'orden' => 1,
            'activo' => true,
        ]);

        Candidato::create([
            'eleccion_id' => $eleccionActiva->id,
            'nombre' => 'María González López',
            'cargo' => 'Presidente',
            'biografia' => 'Experiencia de 7 años en la organización. Especialista en gestión y desarrollo de proyectos comunitarios.',
            'propuestas' => json_encode([
                'Desarrollo comunitario',
                'Inclusión social',
                'Gestión eficiente',
                'Comunicación efectiva',
            ]),
            'orden' => 2,
            'activo' => true,
        ]);

        Candidato::create([
            'eleccion_id' => $eleccionActiva->id,
            'nombre' => 'Carlos Ramírez Torres',
            'cargo' => 'Presidente',
            'biografia' => 'Experiencia de 8 años en la organización. Enfocado en innovación y crecimiento sostenible.',
            'propuestas' => json_encode([
                'Innovación institucional',
                'Sostenibilidad',
                'Alianzas estratégicas',
                'Capacitación continua',
            ]),
            'orden' => 3,
            'activo' => true,
        ]);

        // Candidatos a Vicepresidente
        Candidato::create([
            'eleccion_id' => $eleccionActiva->id,
            'nombre' => 'Ana Martínez Silva',
            'cargo' => 'Vicepresidente',
            'biografia' => 'Comprometida con el apoyo a la presidencia y la coordinación de proyectos estratégicos.',
            'propuestas' => json_encode([
                'Coordinación efectiva',
                'Apoyo institucional',
                'Gestión de proyectos',
            ]),
            'orden' => 1,
            'activo' => true,
        ]);

        Candidato::create([
            'eleccion_id' => $eleccionActiva->id,
            'nombre' => 'Luis Fernando Castro',
            'cargo' => 'Vicepresidente',
            'biografia' => 'Experiencia en trabajo en equipo y coordinación de actividades institucionales.',
            'propuestas' => json_encode([
                'Trabajo colaborativo',
                'Organización eficiente',
                'Seguimiento de objetivos',
            ]),
            'orden' => 2,
            'activo' => true,
        ]);

        // Crear elección programada
        $eleccionProgramada = Eleccion::create([
            'id' => Str::uuid(),
            'organizacion_id' => $organizacion->id,
            'titulo' => 'Elección Comité Especial 2024',
            'descripcion' => 'Elección del comité especial de eventos',
            'tipo' => 'comite',
            'fecha_inicio' => now()->addDays(15),
            'fecha_fin' => now()->addDays(22),
            'start_at' => now()->addDays(15),
            'end_at' => now()->addDays(22),
            'estado' => 'programada',
            'votacion_abierta' => false,
            'created_by' => 1,
        ]);

        Candidato::create([
            'eleccion_id' => $eleccionProgramada->id,
            'nombre' => 'Patricia Sánchez',
            'cargo' => 'Coordinador',
            'biografia' => 'Experiencia en organización de eventos y actividades comunitarias.',
            'propuestas' => json_encode([
                'Eventos innovadores',
                'Mayor participación',
            ]),
            'orden' => 1,
            'activo' => true,
        ]);

        $this->command->info('✅ Elecciones y candidatos creados exitosamente');
        $this->command->info("   - Elección activa: {$eleccionActiva->titulo}");
        $this->command->info("   - Candidatos: 6 creados");
        $this->command->info("   - Elección programada: {$eleccionProgramada->titulo}");
    }
}
