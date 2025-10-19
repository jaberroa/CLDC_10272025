<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organizacion;
use App\Models\Miembro;
use App\Models\Asamblea;
use App\Models\Eleccion;
use App\Models\Capacitacion;
use App\Models\TransaccionFinanciera;
use App\Models\Presupuesto;
use App\Models\DocumentoLegal;
use App\Models\PadronElectoral;
use App\Models\Elector;
use App\Models\PeriodoDirectiva;
use App\Models\AsistenciaAsamblea;
use App\Models\InscripcionCapacitacion;
use App\Models\SeccionalSubmission;
use Illuminate\Support\Str;

class CldciCompleteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Iniciando seeder completo de CLDCI...');

        // 1. Crear organizaciones
        $this->crearOrganizaciones();
        
        // 2. Crear miembros
        $this->crearMiembros();
        
        // 3. Crear períodos de directiva
        $this->crearPeriodosDirectiva();
        
        // 4. Crear asambleas
        $this->crearAsambleas();
        
        // 5. Crear capacitaciones
        $this->crearCapacitaciones();
        
        // 6. Crear transacciones financieras
        $this->crearTransaccionesFinancieras();
        
        // 7. Crear presupuestos
        $this->crearPresupuestos();
        
        // 8. Crear documentos legales
        $this->crearDocumentosLegales();
        
        // 9. Crear padrones electorales
        $this->crearPadronesElectorales();
        
        // 10. Crear elecciones
        $this->crearElecciones();
        
        // 11. Crear seccional submissions
        $this->crearSeccionalSubmissions();

        $this->command->info('✅ Seeder completo de CLDCI finalizado exitosamente!');
    }

    private function crearOrganizaciones()
    {
        $this->command->info('📋 Creando organizaciones...');

        // Organización principal
        $organizacionPrincipal = Organizacion::firstOrCreate(
            ['codigo' => 'CLDCI-001'],
            [
                'nombre' => 'Círculo de Locutores Dominicanos Colegiados, Inc.',
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
            ]
        );

        // Seccionales provinciales
        $provincias = [
            'Santiago', 'La Vega', 'San Cristóbal', 'Puerto Plata', 'La Romana',
            'San Pedro de Macorís', 'Barahona', 'Azua', 'San Juan', 'Monseñor Nouel'
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
                    'estado_adecuacion' => 'aprobada',
                    'miembros_minimos' => 15,
                    'organizacion_padre_id' => $organizacionPrincipal->id,
                ]
            );
        }

        // Seccionales internacionales
        $paisesInternacionales = [
            'Estados Unidos', 'España', 'Venezuela', 'Puerto Rico', 'Canadá'
        ];

        foreach ($paisesInternacionales as $index => $pais) {
            $codigo = 'CLDCI-INT-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
            
            Organizacion::firstOrCreate(
                ['codigo' => $codigo],
                [
                    'nombre' => 'CLDCI Seccional ' . $pais,
                    'tipo' => 'seccional_internacional',
                    'pais' => $pais,
                    'estado_adecuacion' => 'aprobada',
                    'miembros_minimos' => 10,
                    'organizacion_padre_id' => $organizacionPrincipal->id,
                ]
            );
        }

        $this->command->info('✅ Organizaciones creadas: ' . Organizacion::count());
    }

    private function crearMiembros()
    {
        $this->command->info('👥 Creando miembros...');

        $organizacionPrincipal = Organizacion::where('codigo', 'CLDCI-001')->first();
        $seccionales = Organizacion::where('tipo', 'seccional')->get();

        // Miembros de la organización principal
        $miembrosData = [
            [
                'cedula' => '001-1234567-8',
                'nombre_completo' => 'Juan Pérez García',
                'email' => 'juan.perez@cldci.org',
                'telefono' => '(809) 123-4567',
                'profesion' => 'Locutor',
                'tipo_membresia' => 'activo',
                'estado_membresia' => 'activa',
                'fecha_ingreso' => '2020-01-15',
                'numero_carnet' => 'CLDCI-2020-001',
                'organizacion_id' => $organizacionPrincipal->id,
            ],
            [
                'cedula' => '001-9876543-2',
                'nombre_completo' => 'María Rodríguez Soto',
                'email' => 'maria.rodriguez@cldci.org',
                'telefono' => '(809) 765-4321',
                'profesion' => 'Periodista',
                'tipo_membresia' => 'fundador',
                'estado_membresia' => 'activa',
                'fecha_ingreso' => '1990-03-15',
                'numero_carnet' => 'CLDCI-1990-001',
                'organizacion_id' => $organizacionPrincipal->id,
            ],
            [
                'cedula' => '001-1122334-4',
                'nombre_completo' => 'Pedro Gómez Luna',
                'email' => 'pedro.gomez@cldci.org',
                'telefono' => '(809) 222-3333',
                'profesion' => 'Comunicador',
                'tipo_membresia' => 'estudiante',
                'estado_membresia' => 'activa',
                'fecha_ingreso' => '2023-09-01',
                'numero_carnet' => 'CLDCI-2023-005',
                'organizacion_id' => $organizacionPrincipal->id,
            ],
        ];

        foreach ($miembrosData as $data) {
            Miembro::firstOrCreate(
                ['email' => $data['email']],
                $data
            );
        }

        // Miembros para seccionales
        foreach ($seccionales->take(5) as $seccional) {
            for ($i = 1; $i <= 20; $i++) {
                Miembro::firstOrCreate(
                    [
                        'email' => "miembro{$i}@{$seccional->codigo}.org",
                        'organizacion_id' => $seccional->id
                    ],
                    [
                        'cedula' => '001-' . str_pad($i, 7, '0', STR_PAD_LEFT) . '-' . rand(1, 9),
                        'nombre_completo' => "Miembro {$i} de {$seccional->nombre}",
                        'telefono' => '(809) ' . rand(100, 999) . '-' . rand(1000, 9999),
                        'profesion' => ['Locutor', 'Periodista', 'Comunicador'][rand(0, 2)],
                        'tipo_membresia' => ['activo', 'fundador', 'estudiante'][rand(0, 2)],
                        'estado_membresia' => 'activa',
                        'fecha_ingreso' => now()->subDays(rand(30, 365)),
                        'numero_carnet' => $seccional->codigo . '-' . date('Y') . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    ]
                );
            }
        }

        $this->command->info('✅ Miembros creados: ' . Miembro::count());
    }

    private function crearPeriodosDirectiva()
    {
        $this->command->info('🏛️ Creando períodos de directiva...');

        $organizacionPrincipal = Organizacion::where('codigo', 'CLDCI-001')->first();

        PeriodoDirectiva::firstOrCreate(
            [
                'organizacion_id' => $organizacionPrincipal->id,
                'fecha_inicio' => '2024-01-01'
            ],
            [
                'fecha_fin' => '2026-12-31',
                'directiva' => [
                    'presidente' => 'Juan Pérez García',
                    'vicepresidente' => 'María Rodríguez Soto',
                    'secretario' => 'Pedro Gómez Luna',
                    'tesorero' => 'Ana Fernández Díaz',
                    'vocal1' => 'Carlos Sánchez Ruiz',
                    'vocal2' => 'Laura Martínez López',
                ],
                'acta_eleccion_url' => '/documentos/acta-eleccion-2024.pdf',
                'activo' => true,
            ]
        );

        $this->command->info('✅ Períodos de directiva creados: ' . PeriodoDirectiva::count());
    }

    private function crearAsambleas()
    {
        $this->command->info('🏛️ Creando asambleas...');

        $organizacionPrincipal = Organizacion::where('codigo', 'CLDCI-001')->first();

        $asambleasData = [
            [
                'tipo' => 'ordinaria',
                'titulo' => 'Asamblea General Ordinaria 2024',
                'descripcion' => 'Asamblea para aprobar el presupuesto anual y rendición de cuentas',
                'fecha_convocatoria' => now()->subDays(30),
                'fecha_asamblea' => now()->addDays(15),
                'lugar' => 'Sede Nacional CLDCI',
                'modalidad' => 'presencial',
                'quorum_minimo' => 50,
                'estado' => 'convocada',
            ],
            [
                'tipo' => 'especial',
                'titulo' => 'Asamblea Extraordinaria - Modificación Estatutos',
                'descripcion' => 'Asamblea para modificar los estatutos de la organización',
                'fecha_convocatoria' => now()->subDays(15),
                'fecha_asamblea' => now()->addDays(7),
                'lugar' => 'Sede Nacional CLDCI',
                'modalidad' => 'hibrida',
                'enlace_virtual' => 'https://meet.google.com/abc-defg-hij',
                'quorum_minimo' => 75,
                'estado' => 'convocada',
            ],
        ];

        foreach ($asambleasData as $data) {
            $data['organizacion_id'] = $organizacionPrincipal->id;
            Asamblea::firstOrCreate(
                [
                    'titulo' => $data['titulo'],
                    'fecha_asamblea' => $data['fecha_asamblea']
                ],
                $data
            );
        }

        $this->command->info('✅ Asambleas creadas: ' . Asamblea::count());
    }

    private function crearCapacitaciones()
    {
        $this->command->info('🎓 Creando capacitaciones...');

        $organizacionPrincipal = Organizacion::where('codigo', 'CLDCI-001')->first();

        $capacitacionesData = [
            [
                'titulo' => 'Curso de Locución Profesional',
                'descripcion' => 'Capacitación en técnicas de locución y presentación',
                'tipo' => 'curso',
                'modalidad' => 'presencial',
                'fecha_inicio' => now()->addDays(30),
                'fecha_fin' => now()->addDays(35),
                'lugar' => 'Sede Nacional CLDCI',
                'capacidad_maxima' => 25,
                'costo' => 500.00,
                'estado' => 'programada',
            ],
            [
                'titulo' => 'Taller de Periodismo Digital',
                'descripcion' => 'Herramientas y técnicas para periodismo en medios digitales',
                'tipo' => 'taller',
                'modalidad' => 'virtual',
                'fecha_inicio' => now()->addDays(45),
                'fecha_fin' => now()->addDays(47),
                'enlace_virtual' => 'https://meet.google.com/xyz-abc-def',
                'capacidad_maxima' => 50,
                'costo' => 300.00,
                'estado' => 'programada',
            ],
            [
                'titulo' => 'Conferencia: Ética en los Medios',
                'descripcion' => 'Conferencia magistral sobre ética periodística',
                'tipo' => 'conferencia',
                'modalidad' => 'hibrida',
                'fecha_inicio' => now()->addDays(60),
                'lugar' => 'Auditorio Nacional',
                'enlace_virtual' => 'https://youtube.com/live/abc123',
                'capacidad_maxima' => 200,
                'costo' => 0.00,
                'estado' => 'programada',
            ],
        ];

        foreach ($capacitacionesData as $data) {
            $data['organizacion_id'] = $organizacionPrincipal->id;
            Capacitacion::firstOrCreate(
                [
                    'titulo' => $data['titulo'],
                    'fecha_inicio' => $data['fecha_inicio']
                ],
                $data
            );
        }

        $this->command->info('✅ Capacitaciones creadas: ' . Capacitacion::count());
    }

    private function crearTransaccionesFinancieras()
    {
        $this->command->info('💰 Creando transacciones financieras...');

        $organizacionPrincipal = Organizacion::where('codigo', 'CLDCI-001')->first();

        $transaccionesData = [
            // Ingresos
            [
                'tipo' => 'ingreso',
                'categoria' => 'cuotas',
                'concepto' => 'Cuotas de membresía - Enero 2024',
                'monto' => 15000.00,
                'fecha' => now()->subDays(30),
                'metodo_pago' => 'transferencia',
                'referencia' => 'TRF-2024-001',
            ],
            [
                'tipo' => 'ingreso',
                'categoria' => 'eventos',
                'concepto' => 'Inscripciones - Curso de Locución',
                'monto' => 12500.00,
                'fecha' => now()->subDays(15),
                'metodo_pago' => 'efectivo',
            ],
            [
                'tipo' => 'ingreso',
                'categoria' => 'patrocinios',
                'concepto' => 'Patrocinio - Empresa ABC',
                'monto' => 25000.00,
                'fecha' => now()->subDays(10),
                'metodo_pago' => 'transferencia',
                'referencia' => 'TRF-2024-002',
            ],
            // Gastos
            [
                'tipo' => 'gasto',
                'categoria' => 'operativo',
                'concepto' => 'Alquiler sede - Enero 2024',
                'monto' => 8000.00,
                'fecha' => now()->subDays(25),
                'metodo_pago' => 'transferencia',
                'referencia' => 'TRF-2024-003',
            ],
            [
                'tipo' => 'gasto',
                'categoria' => 'eventos',
                'concepto' => 'Catering - Asamblea General',
                'monto' => 3500.00,
                'fecha' => now()->subDays(5),
                'metodo_pago' => 'efectivo',
            ],
        ];

        foreach ($transaccionesData as $data) {
            $data['organizacion_id'] = $organizacionPrincipal->id;
            TransaccionFinanciera::create($data);
        }

        $this->command->info('✅ Transacciones financieras creadas: ' . TransaccionFinanciera::count());
    }

    private function crearPresupuestos()
    {
        $this->command->info('📊 Creando presupuestos...');

        $organizacionPrincipal = Organizacion::where('codigo', 'CLDCI-001')->first();

        $presupuestosData = [
            [
                'periodo' => '2024',
                'categoria' => 'operativo',
                'monto_presupuestado' => 120000.00,
                'monto_ejecutado' => 45000.00,
            ],
            [
                'periodo' => '2024',
                'categoria' => 'eventos',
                'monto_presupuestado' => 80000.00,
                'monto_ejecutado' => 25000.00,
            ],
            [
                'periodo' => '2024',
                'categoria' => 'capacitacion',
                'monto_presupuestado' => 60000.00,
                'monto_ejecutado' => 15000.00,
            ],
            [
                'periodo' => '2024',
                'categoria' => 'equipos',
                'monto_presupuestado' => 40000.00,
                'monto_ejecutado' => 12000.00,
            ],
        ];

        foreach ($presupuestosData as $data) {
            $data['organizacion_id'] = $organizacionPrincipal->id;
            Presupuesto::firstOrCreate(
                [
                    'organizacion_id' => $organizacionPrincipal->id,
                    'periodo' => $data['periodo'],
                    'categoria' => $data['categoria']
                ],
                $data
            );
        }

        $this->command->info('✅ Presupuestos creados: ' . Presupuesto::count());
    }

    private function crearDocumentosLegales()
    {
        $this->command->info('📄 Creando documentos legales...');

        $organizacionPrincipal = Organizacion::where('codigo', 'CLDCI-001')->first();

        $documentosData = [
            [
                'tipo' => 'estatuto',
                'titulo' => 'Estatutos CLDCI 2024',
                'descripcion' => 'Estatutos actualizados de la organización',
                'archivo_url' => '/documentos/estatutos-2024.pdf',
                'fecha_emision' => '2024-01-01',
                'fecha_vigencia' => '2027-12-31',
                'activo' => true,
            ],
            [
                'tipo' => 'reglamento',
                'titulo' => 'Reglamento Interno',
                'descripcion' => 'Reglamento interno de funcionamiento',
                'archivo_url' => '/documentos/reglamento-interno.pdf',
                'fecha_emision' => '2024-01-15',
                'activo' => true,
            ],
            [
                'tipo' => 'acta',
                'titulo' => 'Acta Asamblea General 2023',
                'descripcion' => 'Acta de la asamblea general ordinaria 2023',
                'archivo_url' => '/documentos/acta-asamblea-2023.pdf',
                'fecha_emision' => '2023-12-15',
                'activo' => true,
            ],
        ];

        foreach ($documentosData as $data) {
            $data['organizacion_id'] = $organizacionPrincipal->id;
            DocumentoLegal::firstOrCreate(
                [
                    'titulo' => $data['titulo'],
                    'fecha_emision' => $data['fecha_emision']
                ],
                $data
            );
        }

        $this->command->info('✅ Documentos legales creados: ' . DocumentoLegal::count());
    }

    private function crearPadronesElectorales()
    {
        $this->command->info('🗳️ Creando padrones electorales...');

        $organizacionPrincipal = Organizacion::where('codigo', 'CLDCI-001')->first();
        $miembrosActivos = Miembro::where('organizacion_id', $organizacionPrincipal->id)
            ->where('estado_membresia', 'activa')
            ->get();

        $padron = PadronElectoral::firstOrCreate(
            [
                'organizacion_id' => $organizacionPrincipal->id,
                'periodo' => '2024-2026'
            ],
            [
                'fecha_inicio' => '2024-01-01',
                'fecha_fin' => '2026-12-31',
                'descripcion' => 'Padrón electoral para el período 2024-2026',
                'activo' => true,
            ]
        );

        // Crear electores
        foreach ($miembrosActivos as $miembro) {
            Elector::firstOrCreate(
                [
                    'padron_id' => $padron->id,
                    'miembro_id' => $miembro->id
                ],
                [
                    'elegible' => true,
                ]
            );
        }

        $this->command->info('✅ Padrones electorales creados: ' . PadronElectoral::count());
    }

    private function crearElecciones()
    {
        $this->command->info('🗳️ Creando elecciones...');

        $padron = PadronElectoral::first();

        $organizacionPrincipal = Organizacion::where('codigo', 'CLDCI-001')->first();
        
        if ($organizacionPrincipal) {
            $eleccion = Eleccion::firstOrCreate(
                [
                    'organizacion_id' => $organizacionPrincipal->id,
                    'titulo' => 'Elecciones Presidenciales 2024'
                ],
                [
                    'descripcion' => 'Elecciones para presidente de la organización',
                    'tipo' => 'nacional',
                    'fecha_inicio' => now()->addDays(30),
                    'fecha_fin' => now()->addDays(37),
                    'estado' => 'preparacion',
                    'votos_totales' => 0,
                    'votacion_abierta' => false,
                ]
            );

            $this->command->info('✅ Elecciones creadas: ' . Eleccion::count());
        }
    }

    private function crearSeccionalSubmissions()
    {
        $this->command->info('📋 Creando seccional submissions...');

        $submissionsData = [
            [
                'seccional_nombre' => 'CLDCI Seccional Santiago',
                'directiva' => 'Presidente: Ana García, Secretario: Luis Martínez',
                'miembros_csv_path' => '/expedientes/santiago/miembros.csv',
                'actas_paths' => ['/expedientes/santiago/acta1.pdf', '/expedientes/santiago/acta2.pdf'],
                'miembros_min_ok' => true,
                'miembros_contados' => 25,
                'observaciones' => 'Documentación completa, aprobada',
            ],
            [
                'seccional_nombre' => 'CLDCI Seccional La Vega',
                'directiva' => 'Presidente: Roberto Silva, Secretario: Carmen López',
                'miembros_csv_path' => '/expedientes/lavega/miembros.csv',
                'actas_paths' => ['/expedientes/lavega/acta1.pdf'],
                'miembros_min_ok' => false,
                'miembros_contados' => 12,
                'observaciones' => 'Faltan 3 miembros para cumplir mínimo',
            ],
        ];

        foreach ($submissionsData as $data) {
            SeccionalSubmission::firstOrCreate(
                [
                    'seccional_nombre' => $data['seccional_nombre']
                ],
                $data
            );
        }

        $this->command->info('✅ Seccional submissions creados: ' . SeccionalSubmission::count());
    }
}