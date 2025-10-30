<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadosMembresiaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estadosMembresia = [
            [
                'nombre' => 'activa',
                'descripcion' => 'Membresía activa y al día',
                'color' => 'green',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'suspendida',
                'descripcion' => 'Membresía suspendida temporalmente',
                'color' => 'yellow',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'vencida',
                'descripcion' => 'Membresía vencida',
                'color' => 'red',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'pendiente',
                'descripcion' => 'Membresía pendiente de aprobación',
                'color' => 'blue',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'cancelada',
                'descripcion' => 'Membresía cancelada permanentemente',
                'color' => 'gray',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($estadosMembresia as $estado) {
            DB::table('estados_membresia')->updateOrInsert(
                ['nombre' => $estado['nombre']],
                $estado
            );
        }

        $this->command->info('✅ Estados de membresía creados exitosamente');
    }
}
