<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
                'created_by' => null,
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
                'created_by' => null,
            ],
        ];

        foreach ($asambleasData as $data) {
            $data['id'] = \Illuminate\Support\Str::uuid();
            $data['created_at'] = now();
            $data['updated_at'] = now();
            DB::table('asambleas')->insert($data);
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
                'archivo_url' => '/documentos/resolucion-2025-001.pdf',
                'activo' => true,
                'created_by' => null,
            ],
            [
                'organizacion_id' => $organizacionPrincipal->id,
                'titulo' => 'Reconocimiento a Miembros Destacados',
                'descripcion' => 'Reconocimiento a miembros por su labor excepcional',
                'tipo' => 'circular',
                'numero_documento' => 'CIR-2025-001',
                'fecha_emision' => now()->subDays(10),
                'archivo_url' => '/documentos/circular-2025-001.pdf',
                'activo' => true,
                'created_by' => null,
            ],
        ];

        foreach ($comunicadosData as $data) {
            $data['id'] = \Illuminate\Support\Str::uuid();
            $data['created_at'] = now();
            $data['updated_at'] = now();
            DB::table('documentos_legales')->insert($data);
        }

        // 3. Crear capacitaciones
        $capacitacionesData = [
            [
                'organizacion_id' => $organizacionPrincipal->id,
                'titulo' => 'Curso de Locución Profesional',
                'descripcion' => 'Capacitación en técnicas de locución moderna',
                'tipo' => 'curso',
                'fecha_inicio' => now()->addDays(20),
                'fecha_fin' => now()->addDays(25),
                'lugar' => 'Estudio de Grabación CLDCI',
                'modalidad' => 'presencial',
                'estado' => 'programada',
                'costo' => 5000,
                'capacidad_maxima' => 20,
            ],
            [
                'organizacion_id' => $organizacionPrincipal->id,
                'titulo' => 'Taller de Periodismo Digital',
                'descripcion' => 'Herramientas digitales para periodistas',
                'tipo' => 'taller',
                'fecha_inicio' => now()->addDays(45),
                'fecha_fin' => now()->addDays(47),
                'lugar' => 'Virtual - Google Meet',
                'modalidad' => 'virtual',
                'estado' => 'programada',
                'costo' => 3000,
                'capacidad_maxima' => 50,
            ],
        ];

        foreach ($capacitacionesData as $data) {
            $data['id'] = \Illuminate\Support\Str::uuid();
            $data['created_at'] = now();
            $data['updated_at'] = now();
            DB::table('capacitaciones')->insert($data);
        }

        // 4. Crear elecciones (necesitamos crear un padrón primero)
        $padronId = \Illuminate\Support\Str::uuid();
        DB::table('padrones_electorales')->insert([
            'id' => $padronId,
            'organizacion_id' => $organizacionPrincipal->id,
            'periodo' => '2025-2027',
            'fecha_inicio' => now()->subDays(30),
            'fecha_fin' => now()->addYears(2),
            'descripcion' => 'Padrón electoral para elecciones 2025-2027',
            'activo' => true,
            'total_electores' => 0,
            'created_by' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $eleccionesData = [
            [
                'padron_id' => $padronId,
                'cargo' => 'Presidente',
                'candidatos' => json_encode([
                    ['id' => \Illuminate\Support\Str::uuid(), 'nombre' => 'Juan Pérez', 'propuesta' => 'Modernización de la organización'],
                    ['id' => \Illuminate\Support\Str::uuid(), 'nombre' => 'María García', 'propuesta' => 'Transparencia y participación']
                ]),
                'fecha_inicio' => now()->addDays(60),
                'fecha_fin' => now()->addDays(65),
                'modalidad' => 'virtual',
                'estado' => 'programada',
                'votos_totales' => 0,
                'resultados' => null,
                'auditoria_hash' => null,
                'created_by' => null,
            ],
        ];

        foreach ($eleccionesData as $data) {
            $data['id'] = \Illuminate\Support\Str::uuid();
            $data['created_at'] = now();
            $data['updated_at'] = now();
            DB::table('elecciones')->insert($data);
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
                'aprobado_por' => null,
                'created_by' => null,
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
                'aprobado_por' => null,
                'created_by' => null,
            ],
        ];

        foreach ($transaccionesData as $data) {
            $data['id'] = \Illuminate\Support\Str::uuid();
            $data['created_at'] = now();
            $data['updated_at'] = now();
            DB::table('transacciones_financieras')->insert($data);
        }

        $this->command->info('✅ Datos de noticias creados exitosamente:');
        $this->command->info('   • ' . count($asambleasData) . ' asambleas futuras');
        $this->command->info('   • ' . count($comunicadosData) . ' comunicados de directiva');
        $this->command->info('   • ' . count($capacitacionesData) . ' capacitaciones programadas');
        $this->command->info('   • ' . count($eleccionesData) . ' elecciones próximas');
        $this->command->info('   • ' . count($transaccionesData) . ' transacciones importantes');
    }
}
