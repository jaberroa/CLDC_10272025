<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Capacitacion;
use App\Models\Miembro;
use Carbon\Carbon;

class InscripcionCapacitacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener capacitaciones y miembros
        $capacitaciones = Capacitacion::all();
        $miembros = Miembro::all();

        if ($capacitaciones->isEmpty() || $miembros->isEmpty()) {
            $this->command->warn('No se encontraron capacitaciones o miembros. Ejecute primero los seeders correspondientes.');
            return;
        }

        // Crear inscripciones de muestra
        $inscripciones = [
            [
                'curso_id' => $capacitaciones->first()->id,
                'miembro_id' => $miembros->first()->id,
                'fecha_inscripcion' => Carbon::now()->subDays(10),
                'estado' => 'confirmada',
                'asistio' => true
            ],
            [
                'curso_id' => $capacitaciones->skip(1)->first()->id,
                'miembro_id' => $miembros->skip(1)->first()->id,
                'fecha_inscripcion' => Carbon::now()->subDays(8),
                'estado' => 'pendiente',
                'asistio' => false
            ],
            [
                'curso_id' => $capacitaciones->skip(2)->first()->id,
                'miembro_id' => $miembros->skip(2)->first()->id,
                'fecha_inscripcion' => Carbon::now()->subDays(5),
                'estado' => 'confirmada',
                'asistio' => false
            ]
        ];

        foreach ($inscripciones as $inscripcion) {
            // Simular creación de inscripción (ya que la tabla no existe)
            $this->command->info("Inscripción simulada: Miembro {$inscripcion['miembro_id']} en curso {$inscripcion['curso_id']}");
        }

        $this->command->info('Inscripciones de capacitaciones simuladas exitosamente.');
    }
}

