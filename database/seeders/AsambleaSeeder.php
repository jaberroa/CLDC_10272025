<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AsambleaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear asambleas de prueba
        $asambleas = [
            [
                'titulo' => 'Asamblea General Ordinaria 2025',
                'descripcion' => 'Asamblea general para revisar el estado de la organización',
                'fecha' => now()->addDays(30),
                'hora_inicio' => '09:00:00',
                'hora_fin' => '12:00:00',
                'lugar' => 'Sede Nacional CLDCI',
                'tipo' => 'ordinaria',
                'modalidad' => 'presencial',
                'agenda' => '1. Apertura\n2. Lectura de acta anterior\n3. Informe de gestión\n4. Elecciones\n5. Cierre',
                'activa' => true,
            ],
            [
                'titulo' => 'Asamblea Extraordinaria - Elecciones',
                'descripcion' => 'Asamblea para elección de nueva directiva',
                'fecha' => now()->addDays(45),
                'hora_inicio' => '14:00:00',
                'hora_fin' => '17:00:00',
                'lugar' => 'Centro de Convenciones',
                'tipo' => 'extraordinaria',
                'modalidad' => 'hibrida',
                'enlace_virtual' => 'https://meet.google.com/abc-defg-hij',
                'agenda' => '1. Apertura\n2. Presentación de candidatos\n3. Votación\n4. Proclamación\n5. Cierre',
                'activa' => true,
            ],
            [
                'titulo' => 'Asamblea Virtual - Capacitación',
                'descripcion' => 'Asamblea virtual para capacitación de miembros',
                'fecha' => now()->addDays(60),
                'hora_inicio' => '19:00:00',
                'hora_fin' => '21:00:00',
                'lugar' => 'Virtual',
                'tipo' => 'especial',
                'modalidad' => 'virtual',
                'enlace_virtual' => 'https://zoom.us/j/123456789',
                'agenda' => '1. Apertura\n2. Capacitación en liderazgo\n3. Preguntas y respuestas\n4. Cierre',
                'activa' => true,
            ]
        ];

        foreach ($asambleas as $asamblea) {
            \App\Models\Asamblea::create($asamblea);
        }

        // Crear asistencias de prueba para algunos miembros
        $miembros = \App\Models\Miembro::take(5)->get();
        $asambleas = \App\Models\Asamblea::all();

        foreach ($miembros as $miembro) {
            // Asistir a 1-2 asambleas aleatoriamente
            $asambleasAleatorias = $asambleas->random(rand(1, 2));
            
            foreach ($asambleasAleatorias as $asamblea) {
                \App\Models\AsistenciaAsamblea::create([
                    'miembro_id' => $miembro->id,
                    'asamblea_id' => $asamblea->id,
                    'presente' => rand(0, 1) == 1,
                    'hora_llegada' => $asamblea->fecha . ' ' . $asamblea->hora_inicio,
                    'observaciones' => 'Asistencia de prueba'
                ]);
            }
        }
    }
}
