<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TiposOrganizacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tiposOrganizacion = [
            [
                'nombre' => 'nacional',
                'descripcion' => 'Organización Nacional del CLDCI',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'seccional',
                'descripcion' => 'Seccional Provincial',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'seccional_internacional',
                'descripcion' => 'Seccional Internacional',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'diaspora',
                'descripcion' => 'Organización de Diáspora',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($tiposOrganizacion as $tipo) {
            DB::table('tipos_organizacion')->updateOrInsert(
                ['nombre' => $tipo['nombre']],
                $tipo
            );
        }

        $this->command->info('✅ Tipos de organización creados exitosamente');
    }
}

