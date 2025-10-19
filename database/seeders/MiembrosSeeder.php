<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Miembro;
use App\Models\Organizacion;
use Illuminate\Support\Str;

class MiembrosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener organización principal
        $organizacionPrincipal = Organizacion::where('codigo', 'CLDCI-001')->first();
        
        if (!$organizacionPrincipal) {
            $this->command->error('No se encontró la organización principal CLDCI-001');
            return;
        }

        // Crear miembros de prueba
        $miembros = [
            [
                'nombre_completo' => 'Juan Pérez García',
                'email' => 'juan.perez@cldci.org',
                'cedula' => '001-1234567-8',
                'telefono' => '(809) 123-4567',
                'profesion' => 'Locutor',
                'tipo_membresia' => 'activo',
                'estado_membresia' => 'activa',
                'fecha_ingreso' => '2020-01-15',
                'numero_carnet' => 'CLDCI-2020-001',
            ],
            [
                'nombre_completo' => 'María Rodríguez López',
                'email' => 'maria.rodriguez@cldci.org',
                'cedula' => '001-2345678-9',
                'telefono' => '(809) 234-5678',
                'profesion' => 'Presentadora',
                'tipo_membresia' => 'activo',
                'estado_membresia' => 'activa',
                'fecha_ingreso' => '2019-06-20',
                'numero_carnet' => 'CLDCI-2019-002',
            ],
            [
                'nombre_completo' => 'Carlos Martínez Díaz',
                'email' => 'carlos.martinez@cldci.org',
                'cedula' => '001-3456789-0',
                'telefono' => '(809) 345-6789',
                'profesion' => 'Conductor',
                'tipo_membresia' => 'activo',
                'estado_membresia' => 'activa',
                'fecha_ingreso' => '2021-03-10',
                'numero_carnet' => 'CLDCI-2021-003',
            ],
            [
                'nombre_completo' => 'Ana González Fernández',
                'email' => 'ana.gonzalez@cldci.org',
                'cedula' => '001-4567890-1',
                'telefono' => '(809) 456-7890',
                'profesion' => 'Periodista',
                'tipo_membresia' => 'activo',
                'estado_membresia' => 'suspendida',
                'fecha_ingreso' => '2018-09-15',
                'numero_carnet' => 'CLDCI-2018-004',
            ],
            [
                'nombre_completo' => 'Luis Hernández Torres',
                'email' => 'luis.hernandez@cldci.org',
                'cedula' => '001-5678901-2',
                'telefono' => '(809) 567-8901',
                'profesion' => 'Locutor Deportivo',
                'tipo_membresia' => 'activo',
                'estado_membresia' => 'inactiva',
                'fecha_ingreso' => '2017-12-01',
                'numero_carnet' => 'CLDCI-2017-005',
            ],
        ];

        foreach ($miembros as $miembroData) {
            Miembro::firstOrCreate(
                ['cedula' => $miembroData['cedula']],
                array_merge($miembroData, [
                    'organizacion_id' => $organizacionPrincipal->id,
                ])
            );
        }

        $this->command->info('✅ Miembros creados exitosamente:');
        $this->command->info('   • ' . count($miembros) . ' miembros de prueba');
    }
}