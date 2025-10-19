<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Organizacion;
use Illuminate\Support\Str;

class OrganizacionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener organización principal CLDCI
        $organizacionPrincipal = Organizacion::where('codigo', 'CLDCI-001')->first();
        
        if (!$organizacionPrincipal) {
            $organizacionPrincipal = Organizacion::create([
                'nombre' => 'Círculo de Locutores Dominicanos Colegiados, Inc.',
                'codigo' => 'CLDCI-001',
                'tipo' => 'nacional',
                'pais' => 'República Dominicana',
                'provincia' => 'Distrito Nacional',
                'ciudad' => 'Santo Domingo',
                'direccion' => 'Ave. 27 de Febrero #1405, Plaza de la Cultura, Santo Domingo',
                'telefono' => '(809) 686-2583',
                'email' => 'info@cldci.org.do',
                'estado_adecuacion' => 'aprobada',
                'miembros_minimos' => 100,
                'fecha_fundacion' => '1990-03-15',
            ]);
        }
        
        $this->command->info('Organización principal ID: ' . $organizacionPrincipal->id);

        // Crear seccionales provinciales
        $provincias = [
            'Azua', 'Baoruco', 'Barahona', 'Dajabón', 'Distrito Nacional',
            'Duarte', 'Elías Piña', 'El Seibo', 'Espaillat', 'Hato Mayor',
            'Hermanas Mirabal', 'Independencia', 'La Altagracia', 'La Romana',
            'La Vega', 'María Trinidad Sánchez', 'Monseñor Nouel', 'Monte Cristi',
            'Monte Plata', 'Pedernales', 'Peravia', 'Puerto Plata', 'Samaná',
            'Sánchez Ramírez', 'San Cristóbal', 'San José de Ocoa', 'San Juan',
            'San Pedro de Macorís', 'Santiago', 'Santiago Rodríguez', 'Santo Domingo',
            'Valverde', 'San José de las Matas'
        ];

        foreach ($provincias as $index => $provincia) {
            $codigo = 'CLDCI-' . str_pad($index + 2, 3, '0', STR_PAD_LEFT);
            
            Organizacion::firstOrCreate(
                ['codigo' => $codigo],
                [
                    'nombre' => 'CLDCI Seccional ' . $provincia,
                    'tipo' => 'seccional',
                    'pais' => 'República Dominicana',
                    'provincia' => $provincia,
                    'estado_adecuacion' => 'pendiente',
                    'miembros_minimos' => 15,
                    'organizacion_padre_id' => $organizacionPrincipal->id,
                ]
            );
        }

        // Crear seccionales internacionales (diáspora)
        $paisesInternacionales = [
            'Estados Unidos', 'España', 'Venezuela', 'Puerto Rico', 'Canadá',
            'México', 'Colombia', 'Chile', 'Argentina', 'Brasil'
        ];

        foreach ($paisesInternacionales as $index => $pais) {
            $codigo = 'CLDCI-INT-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
            
            Organizacion::firstOrCreate(
                ['codigo' => $codigo],
                [
                    'nombre' => 'CLDCI Seccional ' . $pais,
                    'tipo' => 'seccional_internacional',
                    'pais' => $pais,
                    'estado_adecuacion' => 'pendiente',
                    'miembros_minimos' => 10,
                    'organizacion_padre_id' => $organizacionPrincipal->id,
                ]
            );
        }

        $this->command->info('✅ Organizaciones creadas exitosamente:');
        $this->command->info('   • 1 organización nacional (CLDCI)');
        $this->command->info('   • ' . count($provincias) . ' seccionales provinciales');
        $this->command->info('   • ' . count($paisesInternacionales) . ' seccionales internacionales');
    }
}