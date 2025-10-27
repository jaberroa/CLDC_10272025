<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Directiva;
use App\Models\Miembro;
use App\Models\Organo;
use App\Models\Cargo;
use App\Models\User;
use Carbon\Carbon;

class DirectivaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener datos existentes
        $miembros = Miembro::all();
        $organos = Organo::all();
        $cargos = Cargo::all();
        $users = User::all();

        if ($miembros->isEmpty() || $organos->isEmpty() || $cargos->isEmpty()) {
            $this->command->warn('No hay suficientes datos para crear directivas. Asegúrese de tener miembros, órganos y cargos.');
            return;
        }

        $directivas = [
            [
                'miembro_id' => $miembros->random()->id,
                'organo_id' => $organos->random()->id,
                'cargo_id' => $cargos->random()->id,
                'periodo_directiva' => '2024-2026',
                'fecha_inicio' => Carbon::now()->subMonths(6),
                'fecha_fin' => Carbon::now()->addMonths(6),
                'estado' => 'activo',
                'observaciones' => 'Directiva activa con mandato vigente',
                'created_by' => $users->isNotEmpty() ? $users->random()->id : null,
            ],
            [
                'miembro_id' => $miembros->random()->id,
                'organo_id' => $organos->random()->id,
                'cargo_id' => $cargos->random()->id,
                'periodo_directiva' => '2023-2025',
                'fecha_inicio' => Carbon::now()->subYear(),
                'fecha_fin' => Carbon::now()->subMonths(3),
                'estado' => 'inactivo',
                'observaciones' => 'Directiva finalizada por término de mandato',
                'created_by' => $users->isNotEmpty() ? $users->random()->id : null,
            ],
            [
                'miembro_id' => $miembros->random()->id,
                'organo_id' => $organos->random()->id,
                'cargo_id' => $cargos->random()->id,
                'periodo_directiva' => '2025-2027',
                'fecha_inicio' => Carbon::now()->subMonths(3),
                'fecha_fin' => null,
                'estado' => 'activo',
                'observaciones' => 'Directiva sin fecha de fin definida',
                'created_by' => $users->isNotEmpty() ? $users->random()->id : null,
            ],
            [
                'miembro_id' => $miembros->random()->id,
                'organo_id' => $organos->random()->id,
                'cargo_id' => $cargos->random()->id,
                'periodo_directiva' => '2024-2026',
                'fecha_inicio' => Carbon::now()->subMonths(2),
                'fecha_fin' => Carbon::now()->addMonths(4),
                'estado' => 'suspendido',
                'observaciones' => 'Directiva suspendida temporalmente',
                'created_by' => $users->isNotEmpty() ? $users->random()->id : null,
            ],
            [
                'miembro_id' => $miembros->random()->id,
                'organo_id' => $organos->random()->id,
                'cargo_id' => $cargos->random()->id,
                'periodo_directiva' => '2025-2027',
                'fecha_inicio' => Carbon::now()->subMonths(1),
                'fecha_fin' => Carbon::now()->addDays(30),
                'estado' => 'activo',
                'observaciones' => 'Directiva próxima a vencer',
                'created_by' => $users->isNotEmpty() ? $users->random()->id : null,
            ],
        ];

        foreach ($directivas as $directivaData) {
            Directiva::firstOrCreate(
                [
                    'miembro_id' => $directivaData['miembro_id'],
                    'organo_id' => $directivaData['organo_id'],
                    'cargo_id' => $directivaData['cargo_id'],
                ],
                $directivaData
            );
        }

        $this->command->info('Directivas creadas exitosamente.');
    }
}