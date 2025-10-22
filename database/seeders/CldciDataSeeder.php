<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CldciDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener la organización principal ya creada
        $organizacionPrincipal = DB::table('organizaciones')->where('codigo', 'CLDCI-001')->first();

        if (!$organizacionPrincipal) {
            $this->command->error('❌ No se encontró la organización principal CLDCI-001. Ejecuta CldciInitialDataSeeder primero.');
            return;
        }

        // Crear miembros de demostración
        $miembrosDemo = [
            [
                'id' => Str::uuid(),
                'user_id' => null,
                'organizacion_id' => $organizacionPrincipal->id,
                'numero_carnet' => 'CLDCI-2025-001',
                'nombre_completo' => 'Dr. Juan Carlos Méndez Pérez',
                'cedula' => '001-0123456-7',
                'email' => 'juan.mendez@cldci.org.do',
                'telefono' => '(809) 555-1001',
                'direccion' => 'Ave. 27 de Febrero #1405, Santo Domingo',
                'fecha_nacimiento' => '1975-03-15',
                'profesion' => 'Locutor Profesional Senior',
                'estado_membresia_id' => DB::table('estados_membresia')->where('nombre', 'activa')->first()->id,
                'fecha_ingreso' => now()->subYears(5)->format('Y-m-d'),
                'fecha_vencimiento' => now()->addYear()->format('Y-m-d'),
                'foto_url' => null,
                'observaciones' => 'Miembro fundador y presidente actual',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'user_id' => null,
                'organizacion_id' => $organizacionPrincipal->id,
                'numero_carnet' => 'CLDCI-2025-002',
                'nombre_completo' => 'Lcda. María Elena Rodríguez Santos',
                'cedula' => '001-0234567-8',
                'email' => 'maria.rodriguez@cldci.org.do',
                'telefono' => '(809) 555-1002',
                'direccion' => 'Calle Las Mercedes #45, Santo Domingo',
                'fecha_nacimiento' => '1980-07-22',
                'profesion' => 'Comunicadora Social',
                'estado_membresia_id' => DB::table('estados_membresia')->where('nombre', 'activa')->first()->id,
                'fecha_ingreso' => now()->subYears(3)->format('Y-m-d'),
                'fecha_vencimiento' => now()->addYear()->format('Y-m-d'),
                'foto_url' => null,
                'observaciones' => 'Secretaria general',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'user_id' => null,
                'organizacion_id' => $organizacionPrincipal->id,
                'numero_carnet' => 'CLDCI-2025-003',
                'nombre_completo' => 'Lic. Roberto Antonio García López',
                'cedula' => '001-0345678-9',
                'email' => 'roberto.garcia@cldci.org.do',
                'telefono' => '(809) 555-1003',
                'direccion' => 'Av. Winston Churchill #123, Santo Domingo',
                'fecha_nacimiento' => '1978-11-08',
                'profesion' => 'Productor de Radio',
                'estado_membresia_id' => DB::table('estados_membresia')->where('nombre', 'activa')->first()->id,
                'fecha_ingreso' => now()->subYears(2)->format('Y-m-d'),
                'fecha_vencimiento' => now()->addYear()->format('Y-m-d'),
                'foto_url' => null,
                'observaciones' => 'Tesorero',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($miembrosDemo as $miembro) {
            DB::table('miembros')->insert($miembro);
        }

        // Crear asambleas de demostración
        $asambleasDemo = [
            [
                'id' => Str::uuid(),
                'organizacion_id' => $organizacionPrincipal->id,
                'tipo' => 'ordinaria',
                'titulo' => 'Asamblea General Ordinaria 2025',
                'descripcion' => 'Asamblea para revisar el estado de la organización y planificar actividades del año',
                'fecha_convocatoria' => now()->subDays(15),
                'fecha_asamblea' => now()->addDays(30),
                'lugar' => 'Sede Nacional CLDCI',
                'modalidad' => 'presencial',
                'quorum_minimo' => 50,
                'estado' => 'convocada',
                'created_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'organizacion_id' => $organizacionPrincipal->id,
                'tipo' => 'extraordinaria',
                'titulo' => 'Asamblea Extraordinaria - Modificación de Estatutos',
                'descripcion' => 'Asamblea para discutir y aprobar modificaciones a los estatutos',
                'fecha_convocatoria' => now()->subDays(5),
                'fecha_asamblea' => now()->addDays(15),
                'lugar' => 'Virtual - Zoom',
                'modalidad' => 'virtual',
                'quorum_minimo' => 30,
                'estado' => 'convocada',
                'created_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($asambleasDemo as $asamblea) {
            DB::table('asambleas')->insert($asamblea);
        }

        // Crear capacitaciones de demostración
        $capacitacionesDemo = [
            [
                'id' => Str::uuid(),
                'organizacion_id' => $organizacionPrincipal->id,
                'titulo' => 'Curso de Locución Profesional',
                'descripcion' => 'Capacitación intensiva para locutores profesionales',
                'tipo' => 'curso',
                'modalidad' => 'presencial',
                'fecha_inicio' => now()->addDays(20),
                'fecha_fin' => now()->addDays(25),
                'lugar' => 'Estudio de Grabación CLDCI',
                'capacidad_maxima' => 20,
                'costo' => 1500.00,
                'estado' => 'programada',
                'created_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'organizacion_id' => $organizacionPrincipal->id,
                'titulo' => 'Taller de Oratoria Moderna',
                'descripcion' => 'Técnicas avanzadas de oratoria para comunicadores',
                'tipo' => 'taller',
                'modalidad' => 'virtual',
                'fecha_inicio' => now()->addDays(40),
                'fecha_fin' => now()->addDays(42),
                'lugar' => 'Virtual - Google Meet',
                'capacidad_maxima' => 30,
                'costo' => 800.00,
                'estado' => 'programada',
                'created_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($capacitacionesDemo as $capacitacion) {
            DB::table('capacitaciones')->insert($capacitacion);
        }

        // Crear transacciones financieras de demostración
        $transaccionesDemo = [
            [
                'id' => Str::uuid(),
                'organizacion_id' => $organizacionPrincipal->id,
                'tipo' => 'ingreso',
                'categoria' => 'cuotas',
                'concepto' => 'Cuotas de Membresía - Enero 2025',
                'monto' => 125000.00,
                'fecha' => now()->subDays(2)->format('Y-m-d'),
                'metodo_pago' => 'transferencia',
                'referencia' => 'CUOTAS-2025-01',
                'observaciones' => 'Recaudación de cuotas de membresía del mes de enero',
                'created_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'organizacion_id' => $organizacionPrincipal->id,
                'tipo' => 'gasto',
                'categoria' => 'operativo',
                'concepto' => 'Alquiler de Oficina',
                'monto' => 45000.00,
                'fecha' => now()->subDays(1)->format('Y-m-d'),
                'metodo_pago' => 'transferencia',
                'referencia' => 'ALQ-2025-01',
                'observaciones' => 'Pago mensual de alquiler de oficina',
                'created_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($transaccionesDemo as $transaccion) {
            DB::table('transacciones_financieras')->insert($transaccion);
        }

        $this->command->info('✅ Datos de demostración de CLDCI creados exitosamente');
        $this->command->info('   • ' . count($miembrosDemo) . ' miembros de demostración');
        $this->command->info('   • ' . count($asambleasDemo) . ' asambleas de demostración');
        $this->command->info('   • ' . count($capacitacionesDemo) . ' capacitaciones de demostración');
        $this->command->info('   • ' . count($transaccionesDemo) . ' transacciones de demostración');
    }
}