<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CldciInitialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Poblar tipos de organización
        $tiposOrganizacion = [
            ['nombre' => 'seccional_nacional', 'descripcion' => 'Seccional Nacional'],
            ['nombre' => 'seccional', 'descripcion' => 'Seccional Provincial'],
            ['nombre' => 'seccional_internacional', 'descripcion' => 'Seccional Internacional'],
            ['nombre' => 'asociacion', 'descripcion' => 'Asociación'],
            ['nombre' => 'gremio', 'descripcion' => 'Gremio'],
            ['nombre' => 'sindicato', 'descripcion' => 'Sindicato'],
            ['nombre' => 'otra_entidad', 'descripcion' => 'Otra Entidad'],
        ];

        foreach ($tiposOrganizacion as $tipo) {
            DB::table('tipos_organizacion')->insert([
                'nombre' => $tipo['nombre'],
                'descripcion' => $tipo['descripcion'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Poblar estados de membresía
        $estadosMembresia = [
            ['nombre' => 'activa', 'descripcion' => 'Membresía Activa'],
            ['nombre' => 'vencida', 'descripcion' => 'Membresía Vencida'],
            ['nombre' => 'pendiente', 'descripcion' => 'Membresía Pendiente'],
            ['nombre' => 'suspendida', 'descripcion' => 'Membresía Suspendida'],
        ];

        foreach ($estadosMembresia as $estado) {
            DB::table('estados_membresia')->insert([
                'nombre' => $estado['nombre'],
                'descripcion' => $estado['descripcion'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Poblar estados de adecuación
        $estadosAdecuacion = [
            ['nombre' => 'pendiente', 'descripcion' => 'Pendiente de Revisión'],
            ['nombre' => 'en_revision', 'descripcion' => 'En Revisión'],
            ['nombre' => 'aprobada', 'descripcion' => 'Aprobada'],
            ['nombre' => 'rechazada', 'descripcion' => 'Rechazada'],
        ];

        foreach ($estadosAdecuacion as $estado) {
            DB::table('estados_adecuacion')->insert([
                'nombre' => $estado['nombre'],
                'descripcion' => $estado['descripcion'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Poblar roles de aplicación
        $appRoles = [
            ['nombre' => 'admin', 'descripcion' => 'Administrador del Sistema'],
            ['nombre' => 'moderador', 'descripcion' => 'Moderador de Organización'],
            ['nombre' => 'miembro', 'descripcion' => 'Miembro Regular'],
        ];

        foreach ($appRoles as $rol) {
            DB::table('app_roles')->insert([
                'nombre' => $rol['nombre'],
                'descripcion' => $rol['descripcion'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Crear organización principal CLDCI
        $organizacionPrincipalId = Str::uuid();
        DB::table('organizaciones')->insert([
            'id' => $organizacionPrincipalId,
            'nombre' => 'Círculo de Locutores Dominicanos Colegiados, Inc.',
            'tipo_organizacion_id' => DB::table('tipos_organizacion')->where('nombre', 'seccional_nacional')->first()->id,
            'codigo' => 'CLDCI-001',
            'pais' => 'República Dominicana',
            'provincia' => 'Distrito Nacional',
            'ciudad' => 'Santo Domingo',
            'direccion' => 'Ave. 27 de Febrero #1405, Plaza de la Cultura',
            'telefono' => '(809) 686-2583',
            'email' => 'info@cldci.org.do',
            'fecha_fundacion' => '1990-03-15',
            'estado_adecuacion_id' => DB::table('estados_adecuacion')->where('nombre', 'aprobada')->first()->id,
            'miembros_minimos' => 100,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Crear seccionales provinciales
        $provincias = [
            'Azua', 'Baoruco', 'Barahona', 'Dajabón', 'Duarte', 'Elías Piña', 'El Seibo', 'Espaillat',
            'Hato Mayor', 'Hermanas Mirabal', 'Independencia', 'La Altagracia', 'La Romana', 'La Vega',
            'María Trinidad Sánchez', 'Monseñor Nouel', 'Monte Cristi', 'Monte Plata', 'Pedernales',
            'Peravia', 'Puerto Plata', 'Samaná', 'Sánchez Ramírez', 'San Cristóbal', 'San José de Ocoa',
            'San Juan', 'San Pedro de Macorís', 'Santiago', 'Santiago Rodríguez', 'Santo Domingo',
            'Valverde'
        ];

        $contador = 1;
        foreach ($provincias as $provincia) {
            DB::table('organizaciones')->insert([
                'id' => Str::uuid(),
                'nombre' => 'CLDCI Seccional ' . $provincia,
                'tipo_organizacion_id' => DB::table('tipos_organizacion')->where('nombre', 'seccional')->first()->id,
                'codigo' => 'CLDCI-S' . str_pad($contador, 2, '0', STR_PAD_LEFT),
                'pais' => 'República Dominicana',
                'provincia' => $provincia,
                'ciudad' => $provincia,
                'estado_adecuacion_id' => DB::table('estados_adecuacion')->where('nombre', 'pendiente')->first()->id,
                'miembros_minimos' => 15,
                'organizacion_padre_id' => $organizacionPrincipalId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $contador++;
        }

        // Crear seccionales internacionales
        $seccionalesInternacionales = [
            ['pais' => 'Estados Unidos', 'ciudad' => 'Miami', 'codigo' => 'CLDCI-SI-MIA'],
            ['pais' => 'Estados Unidos', 'ciudad' => 'Nueva York', 'codigo' => 'CLDCI-SI-NY'],
            ['pais' => 'España', 'ciudad' => 'Madrid', 'codigo' => 'CLDCI-SI-MAD'],
        ];

        foreach ($seccionalesInternacionales as $seccional) {
            DB::table('organizaciones')->insert([
                'id' => Str::uuid(),
                'nombre' => 'CLDCI Seccional Internacional ' . $seccional['ciudad'],
                'tipo_organizacion_id' => DB::table('tipos_organizacion')->where('nombre', 'seccional_internacional')->first()->id,
                'codigo' => $seccional['codigo'],
                'pais' => $seccional['pais'],
                'ciudad' => $seccional['ciudad'],
                'estado_adecuacion_id' => DB::table('estados_adecuacion')->where('nombre', 'aprobada')->first()->id,
                'miembros_minimos' => 10,
                'organizacion_padre_id' => $organizacionPrincipalId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ Datos iniciales de CLDCI creados exitosamente');
        $this->command->info('   • ' . count($tiposOrganizacion) . ' tipos de organización');
        $this->command->info('   • ' . count($estadosMembresia) . ' estados de membresía');
        $this->command->info('   • ' . count($estadosAdecuacion) . ' estados de adecuación');
        $this->command->info('   • ' . count($appRoles) . ' roles de aplicación');
        $this->command->info('   • 1 organización principal');
        $this->command->info('   • ' . count($provincias) . ' seccionales provinciales');
        $this->command->info('   • ' . count($seccionalesInternacionales) . ' seccionales internacionales');
    }
}


