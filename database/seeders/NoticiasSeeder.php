<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Asamblea;
use App\Models\Capacitacion;
use App\Models\Eleccion;
use App\Models\DocumentoLegal;
use App\Models\TransaccionFinanciera;
use App\Models\Organizacion;
use Illuminate\Support\Str;

class NoticiasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organizacionPrincipal = Organizacion::where('codigo', 'CLDCI-001')->first();

        if (!$organizacionPrincipal) {
            $this->command->error('❌ No se encontró la organización principal CLDCI-001. Ejecuta OrganizacionesSeeder primero.');
            return;
        }

        // 1. Crear asambleas futuras
        $asambleasData = [
            [
                'organizacion_id' => $organizacionPrincipal->id,
                'tipo' => 'extraordinaria',
                'titulo' => 'Asamblea General Extraordinaria',
                'descripcion' => 'Asamblea para discutir cambios en los estatutos',
                'fecha_convocatoria' => now()->subDays(5),
                'fecha_asamblea' => now()->addDays(15),
                'quorum_minimo' => 50,
                'lugar' => 'Sede Nacional CLDCI',
                'modalidad' => 'presencial',
                'estado' => 'convocada',
                'created_by' => 'admin',
            ],
            [
                'organizacion_id' => $organizacionPrincipal->id,
                'tipo' => 'ordinaria',
                'titulo' => 'Asamblea Ordinaria Mensual',
                'descripcion' => 'Asamblea mensual de seguimiento',
                'fecha_convocatoria' => now()->subDays(2),
                'fecha_asamblea' => now()->addDays(30),
                'quorum_minimo' => 30,
                'lugar' => 'Virtual - Zoom',
                'modalidad' => 'virtual',
                'estado' => 'convocada',
                'created_by' => 'admin',
            ],
        ];

        foreach ($asambleasData as $data) {
            Asamblea::firstOrCreate(
                ['titulo' => $data['titulo']],
                $data
            );
        }

        // 2. Crear comunicados de directiva
        $comunicadosData = [
            [
                'organizacion_id' => $organizacionPrincipal->id,
                'titulo' => 'Nueva Política de Membresía',
                'descripcion' => 'Se establecen nuevos requisitos para la membresía activa',
                'tipo' => 'resolucion',
                'numero_documento' => 'RES-2025-001',
                'fecha_emision' => now()->subDays(5),
                'activo' => true,
                'created_by' => 'admin',
            ],
            [
                'organizacion_id' => $organizacionPrincipal->id,
                'titulo' => 'Reconocimiento a Miembros Destacados',
                'descripcion' => 'Reconocimiento a miembros por su labor excepcional',
                'tipo' => 'circular',
                'numero_documento' => 'CIR-2025-001',
                'fecha_emision' => now()->subDays(10),
                'activo' => true,
                'created_by' => 'admin',
            ],
        ];

        foreach ($comunicadosData as $data) {
            DocumentoLegal::firstOrCreate(
                ['titulo' => $data['titulo']],
                $data
            );
        }

        // 3. Crear capacitaciones
        $capacitacionesData = [
            [
                'organizacion_id' => $organizacionPrincipal->id,
                'titulo' => 'Curso de Locución Profesional',
                'descripcion' => 'Capacitación en técnicas de locución moderna',
                'fecha_inicio' => now()->addDays(20),
                'fecha_fin' => now()->addDays(25),
                'lugar' => 'Estudio de Grabación CLDCI',
                'modalidad' => 'presencial',
                'estado' => 'programada',
                'costo' => 5000,
                'cupo_maximo' => 20,
                'instructor' => 'Prof. María González',
            ],
            [
                'organizacion_id' => $organizacionPrincipal->id,
                'titulo' => 'Taller de Periodismo Digital',
                'descripcion' => 'Herramientas digitales para periodistas',
                'fecha_inicio' => now()->addDays(45),
                'fecha_fin' => now()->addDays(47),
                'lugar' => 'Virtual - Google Meet',
                'modalidad' => 'virtual',
                'estado' => 'programada',
                'costo' => 3000,
                'cupo_maximo' => 50,
                'instructor' => 'Lic. Carlos Rodríguez',
            ],
        ];

        foreach ($capacitacionesData as $data) {
            Capacitacion::firstOrCreate(
                ['titulo' => $data['titulo']],
                $data
            );
        }

        // 4. Crear elecciones
        $eleccionesData = [
            [
                'organizacion_id' => $organizacionPrincipal->id,
                'titulo' => 'Elecciones Directiva Nacional 2025',
                'descripcion' => 'Proceso electoral para elegir nueva directiva',
                'tipo' => 'nacional',
                'fecha_inicio' => now()->addDays(60),
                'fecha_fin' => now()->addDays(65),
                'estado' => 'preparacion',
                'votos_totales' => 0,
                'votacion_abierta' => false,
                'created_by' => 'admin',
            ],
        ];

        foreach ($eleccionesData as $data) {
            Eleccion::firstOrCreate(
                ['titulo' => $data['titulo']],
                $data
            );
        }

        // 5. Crear transacciones financieras importantes
        $transaccionesData = [
            [
                'organizacion_id' => $organizacionPrincipal->id,
                'tipo' => 'ingreso',
                'concepto' => 'Cuotas de Membresía - Enero 2025',
                'monto' => 125000,
                'categoria' => 'membresia',
                'fecha' => now()->subDays(2),
                'observaciones' => 'Recaudación de cuotas de membresía del mes de enero',
                'referencia' => 'CUOTAS-2025-01',
                'metodo_pago' => 'transferencia',
                'aprobado_por' => 'admin',
                'created_by' => 'admin',
            ],
            [
                'organizacion_id' => $organizacionPrincipal->id,
                'tipo' => 'ingreso',
                'concepto' => 'Donación Empresarial',
                'monto' => 75000,
                'categoria' => 'donacion',
                'fecha' => now()->subDays(5),
                'observaciones' => 'Donación de empresa patrocinadora',
                'referencia' => 'DON-2025-001',
                'metodo_pago' => 'cheque',
                'aprobado_por' => 'admin',
                'created_by' => 'admin',
            ],
        ];

        foreach ($transaccionesData as $data) {
            TransaccionFinanciera::firstOrCreate(
                ['referencia' => $data['referencia']],
                $data
            );
        }

        $this->command->info('✅ Datos de noticias creados exitosamente:');
        $this->command->info('   • ' . count($asambleasData) . ' asambleas futuras');
        $this->command->info('   • ' . count($comunicadosData) . ' comunicados de directiva');
        $this->command->info('   • ' . count($capacitacionesData) . ' capacitaciones programadas');
        $this->command->info('   • ' . count($eleccionesData) . ' elecciones próximas');
        $this->command->info('   • ' . count($transaccionesData) . ' transacciones importantes');
    }
}
