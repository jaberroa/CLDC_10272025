<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Organizacion;
use App\Models\Miembro;
use App\Models\EstadoMembresia;
use App\Models\TipoOrganizacion;
use App\Models\CuotaMembresia;
use App\Models\Organo;
use App\Models\Cargo;
use App\Models\MiembroDirectivo;
use App\Models\Asamblea;
use App\Models\AsistenciaAsamblea;
use App\Models\Curso;
use App\Models\InscripcionCurso;
use App\Models\Noticia;
use App\Models\CarnetTemplate;
use App\Models\TransaccionFinanciera;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CldciRefactoredSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Iniciando seeders refactorizados...');

        // 1. Crear datos de referencia
        $this->crearDatosReferencia();
        
        // 2. Crear organizaciones
        $this->crearOrganizaciones();
        
        // 3. Crear usuarios
        $this->crearUsuarios();
        
        // 4. Crear miembros
        $this->crearMiembros();
        
        // 5. Crear cuotas
        $this->crearCuotas();
        
        // 6. Crear directiva
        $this->crearDirectiva();
        
        // 7. Crear asambleas
        $this->crearAsambleas();
        
        // 8. Crear cursos
        $this->crearCursos();
        
        // 9. Crear noticias
        $this->crearNoticias();
        
        // 10. Crear templates de carnet
        $this->crearTemplatesCarnet();
        
        // 11. Crear transacciones financieras
        $this->crearTransaccionesFinancieras();

        $this->command->info('✅ Seeders refactorizados completados exitosamente!');
    }

    private function crearDatosReferencia()
    {
        $this->command->info('📋 Creando datos de referencia...');

        // Estados de membresía
        EstadoMembresia::firstOrCreate(['nombre' => 'Activa'], ['descripcion' => 'Membresía activa', 'color' => '#28a745']);
        EstadoMembresia::firstOrCreate(['nombre' => 'Inactiva'], ['descripcion' => 'Membresía inactiva', 'color' => '#6c757d']);
        EstadoMembresia::firstOrCreate(['nombre' => 'Suspendida'], ['descripcion' => 'Membresía suspendida', 'color' => '#dc3545']);
        EstadoMembresia::firstOrCreate(['nombre' => 'Vencida'], ['descripcion' => 'Membresía vencida', 'color' => '#fd7e14']);

        // Tipos de organización
        TipoOrganizacion::firstOrCreate(['nombre' => 'Seccional Nacional'], ['descripcion' => 'Seccional a nivel nacional']);
        TipoOrganizacion::firstOrCreate(['nombre' => 'Seccional Provincial'], ['descripcion' => 'Seccional a nivel provincial']);
        TipoOrganizacion::firstOrCreate(['nombre' => 'Seccional Regional'], ['descripcion' => 'Seccional a nivel regional']);
        TipoOrganizacion::firstOrCreate(['nombre' => 'Seccional Internacional'], ['descripcion' => 'Seccional internacional']);

        $this->command->info('✅ Datos de referencia creados');
    }

    private function crearOrganizaciones()
    {
        $this->command->info('🏢 Creando organizaciones...');

        // Organización principal
        Organizacion::firstOrCreate(['codigo' => 'CLDCI-001'], [
            'nombre' => 'Colegio de Locutores y Comunicadores de la República Dominicana',
            'tipo' => 'nacional',
            'estado' => 'activa',
            'descripcion' => 'Organización principal a nivel nacional',
            'direccion' => 'Av. 27 de Febrero, Santo Domingo',
            'telefono' => '(809) 123-4567',
            'email' => 'info@cldci.org'
        ]);

        // Seccionales provinciales
        $seccionales = [
            ['nombre' => 'CLDCI Seccional Santo Domingo', 'codigo' => 'CLDCI-SD', 'tipo' => 'seccional'],
            ['nombre' => 'CLDCI Seccional Santiago', 'codigo' => 'CLDCI-ST', 'tipo' => 'seccional'],
            ['nombre' => 'CLDCI Seccional La Romana', 'codigo' => 'CLDCI-LR', 'tipo' => 'seccional'],
            ['nombre' => 'CLDCI Seccional San Pedro', 'codigo' => 'CLDCI-SP', 'tipo' => 'seccional'],
            ['nombre' => 'CLDCI Seccional Puerto Plata', 'codigo' => 'CLDCI-PP', 'tipo' => 'seccional']
        ];

        foreach ($seccionales as $seccional) {
            Organizacion::firstOrCreate(['codigo' => $seccional['codigo']], array_merge($seccional, [
                'estado' => 'activa',
                'descripcion' => 'Seccional provincial',
                'telefono' => '(809) ' . rand(100, 999) . '-' . rand(1000, 9999),
                'email' => strtolower(str_replace(' ', '', $seccional['codigo'])) . '@cldci.org'
            ]));
        }

        $this->command->info('✅ Organizaciones creadas');
    }

    private function crearUsuarios()
    {
        $this->command->info('👥 Creando usuarios...');

        // Usuario administrador
        User::create([
            'name' => 'Administrador CLDCI',
            'email' => 'admin@cldci.org',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'active' => true
        ]);

        // Usuarios de acceso rápido
        User::create([
            'name' => 'Usuario Miembros',
            'email' => 'miembros@cldci.org',
            'password' => Hash::make('miembros123'),
            'role' => 'miembro',
            'active' => true
        ]);

        User::create([
            'name' => 'Usuario Directiva',
            'email' => 'directiva@cldci.org',
            'password' => Hash::make('directiva123'),
            'role' => 'directivo',
            'active' => true
        ]);

        $this->command->info('✅ Usuarios creados');
    }

    private function crearMiembros()
    {
        $this->command->info('👤 Creando miembros...');

        $organizacion = Organizacion::where('codigo', 'CLDCI-001')->first();
        $estadoActivo = EstadoMembresia::where('nombre', 'Activa')->first();

        $miembros = [
            [
                'nombre_completo' => 'Juan Pérez García',
                'cedula' => '001-1234567-8',
                'email' => 'juan.perez@cldci.org',
                'telefono' => '(809) 123-4567',
                'profesion' => 'Locutor',
                'fecha_ingreso' => '2020-01-15',
                'numero_carnet' => 'CLDCI-2020-001'
            ],
            [
                'nombre_completo' => 'María Rodríguez López',
                'cedula' => '001-2345678-9',
                'email' => 'maria.rodriguez@cldci.org',
                'telefono' => '(809) 234-5678',
                'profesion' => 'Presentadora',
                'fecha_ingreso' => '2019-06-20',
                'numero_carnet' => 'CLDCI-2019-002'
            ],
            [
                'nombre_completo' => 'Carlos Martínez Torres',
                'cedula' => '001-3456789-0',
                'email' => 'carlos.martinez@cldci.org',
                'telefono' => '(809) 345-6789',
                'profesion' => 'Locutor Deportivo',
                'fecha_ingreso' => '2021-03-10',
                'numero_carnet' => 'CLDCI-2021-003'
            ],
            [
                'nombre_completo' => 'Ana González Fernández',
                'cedula' => '001-4567890-1',
                'email' => 'ana.gonzalez@cldci.org',
                'telefono' => '(809) 456-7890',
                'profesion' => 'Periodista',
                'fecha_ingreso' => '2018-09-15',
                'numero_carnet' => 'CLDCI-2018-004'
            ],
            [
                'nombre_completo' => 'Luis Hernández Torres',
                'cedula' => '001-5678901-2',
                'email' => 'luis.hernandez@cldci.org',
                'telefono' => '(809) 567-8901',
                'profesion' => 'Locutor Deportivo',
                'fecha_ingreso' => '2017-12-01',
                'numero_carnet' => 'CLDCI-2017-005'
            ]
        ];

        foreach ($miembros as $miembroData) {
            Miembro::firstOrCreate(['cedula' => $miembroData['cedula']], array_merge($miembroData, [
                'organizacion_id' => $organizacion->id,
                'estado_membresia_id' => $estadoActivo->id,
                'fecha_vencimiento' => now()->addYear()
            ]));
        }

        $this->command->info('✅ Miembros creados');
    }

    private function crearCuotas()
    {
        $this->command->info('💰 Creando cuotas...');

        $miembros = Miembro::all();
        $tiposCuota = ['mensual', 'trimestral', 'anual'];
        $montos = [500, 1500, 6000]; // RD$ por tipo

        foreach ($miembros as $miembro) {
            // Generar cuotas para el año actual
            for ($i = 1; $i <= 12; $i++) {
                $tipoIndex = rand(0, 2);
                $fechaVencimiento = now()->addMonths($i);
                $estado = rand(0, 1) ? 'pagada' : 'pendiente';
                $fechaPago = $estado === 'pagada' ? $fechaVencimiento->copy()->subDays(rand(1, 15)) : null;

                CuotaMembresia::firstOrCreate([
                    'miembro_id' => $miembro->id,
                    'tipo_cuota' => $tiposCuota[$tipoIndex],
                    'fecha_vencimiento' => $fechaVencimiento
                ], [
                    'monto' => $montos[$tipoIndex],
                    'fecha_pago' => $fechaPago,
                    'estado' => $estado
                ]);
            }
        }

        $this->command->info('✅ Cuotas creadas');
    }

    private function crearDirectiva()
    {
        $this->command->info('👔 Creando directiva...');

        // Órganos
        $organos = [
            ['nombre' => 'Junta Directiva Nacional', 'descripcion' => 'Órgano máximo de dirección', 'tipo' => 'directiva', 'nivel' => 'nacional'],
            ['nombre' => 'Comisión de Ética', 'descripcion' => 'Comisión de ética profesional', 'tipo' => 'comision', 'nivel' => 'nacional'],
            ['nombre' => 'Comité de Capacitación', 'descripcion' => 'Comité de formación profesional', 'tipo' => 'comite', 'nivel' => 'nacional']
        ];

        foreach ($organos as $organoData) {
            Organo::create($organoData);
        }

        // Cargos
        $cargos = [
            ['nombre' => 'Presidente', 'descripcion' => 'Presidente de la organización', 'nivel' => 'nacional'],
            ['nombre' => 'Vicepresidente', 'descripcion' => 'Vicepresidente de la organización', 'nivel' => 'nacional'],
            ['nombre' => 'Secretario', 'descripcion' => 'Secretario de la organización', 'nivel' => 'nacional'],
            ['nombre' => 'Tesorero', 'descripcion' => 'Tesorero de la organización', 'nivel' => 'nacional'],
            ['nombre' => 'Vocal', 'descripcion' => 'Vocal de la organización', 'nivel' => 'nacional']
        ];

        foreach ($cargos as $cargoData) {
            Cargo::create($cargoData);
        }

        // Asignar directivos
        $miembros = Miembro::take(5)->get();
        $organo = Organo::where('nombre', 'Junta Directiva Nacional')->first();
        $cargos = Cargo::all();

        foreach ($miembros as $index => $miembro) {
            if ($index < count($cargos)) {
                MiembroDirectivo::create([
                    'miembro_id' => $miembro->id,
                    'organo_id' => $organo->id,
                    'cargo_id' => $cargos[$index]->id,
                    'fecha_inicio' => now()->subMonths(rand(1, 12)),
                    'estado' => 'activo',
                    'es_presidente' => $index === 0
                ]);
            }
        }

        $this->command->info('✅ Directiva creada');
    }

    private function crearAsambleas()
    {
        $this->command->info('🏛️ Creando asambleas...');

        $organizacion = Organizacion::where('codigo', 'CLDCI-001')->first();
        $admin = User::where('email', 'admin@cldci.org')->first();

        $asambleas = [
            [
                'titulo' => 'Asamblea General Ordinaria 2025',
                'descripcion' => 'Asamblea general para revisar el estado de la organización',
                'fecha_convocatoria' => now(),
                'fecha_asamblea' => now()->addDays(30),
                'lugar' => 'Sede Nacional CLDCI',
                'tipo' => 'ordinaria',
                'modalidad' => 'presencial',
                'quorum_minimo' => 10
            ],
            [
                'titulo' => 'Asamblea Extraordinaria - Elecciones',
                'descripcion' => 'Asamblea para elección de nueva directiva',
                'fecha_convocatoria' => now(),
                'fecha_asamblea' => now()->addDays(45),
                'lugar' => 'Centro de Convenciones',
                'tipo' => 'extraordinaria',
                'modalidad' => 'hibrida',
                'enlace_virtual' => 'https://meet.google.com/abc-defg-hij',
                'quorum_minimo' => 15
            ],
            [
                'titulo' => 'Asamblea Virtual - Capacitación',
                'descripcion' => 'Asamblea virtual para capacitación de miembros',
                'fecha_convocatoria' => now(),
                'fecha_asamblea' => now()->addDays(60),
                'lugar' => 'Virtual',
                'tipo' => 'especial',
                'modalidad' => 'virtual',
                'enlace_virtual' => 'https://zoom.us/j/123456789',
                'quorum_minimo' => 8
            ]
        ];

        foreach ($asambleas as $asambleaData) {
            $asamblea = Asamblea::create(array_merge($asambleaData, [
                'organizacion_id' => $organizacion->id,
                'created_by' => $admin->id
            ]));

            // Crear asistencias aleatorias
            $miembros = Miembro::take(rand(3, 5))->get();
            foreach ($miembros as $miembro) {
                AsistenciaAsamblea::create([
                    'asamblea_id' => $asamblea->id,
                    'miembro_id' => $miembro->id,
                    'presente' => rand(0, 1) == 1,
                    'modalidad' => $asamblea->modalidad
                ]);
            }
        }

        $this->command->info('✅ Asambleas creadas');
    }

    private function crearCursos()
    {
        $this->command->info('📚 Creando cursos...');

        $cursos = [
            [
                'titulo' => 'Liderazgo Comunitario Avanzado',
                'descripcion' => 'Curso especializado en técnicas de liderazgo para dirigentes comunitarios',
                'fecha_inicio' => now()->addDays(15),
                'fecha_fin' => now()->addDays(17),
                'modalidad' => 'presencial',
                'lugar' => 'Centro de Capacitación CLDCI',
                'cupo_maximo' => 30,
                'costo' => 0,
                'instructor' => 'Dr. Juan Pérez',
                'contenido' => 'Módulo 1: Fundamentos del liderazgo\nMódulo 2: Comunicación efectiva\nMódulo 3: Resolución de conflictos'
            ],
            [
                'titulo' => 'Gestión de Proyectos Sociales',
                'descripcion' => 'Metodologías para la planificación y ejecución de proyectos comunitarios',
                'fecha_inicio' => now()->addDays(25),
                'fecha_fin' => now()->addDays(27),
                'modalidad' => 'virtual',
                'enlace_virtual' => 'https://meet.google.com/abc-defg-hij',
                'cupo_maximo' => 50,
                'costo' => 0,
                'instructor' => 'Lic. María Rodríguez',
                'contenido' => 'Módulo 1: Planificación de proyectos\nMódulo 2: Ejecución y seguimiento\nMódulo 3: Evaluación y cierre'
            ],
            [
                'titulo' => 'Comunicación Efectiva',
                'descripcion' => 'Técnicas de comunicación para líderes comunitarios',
                'fecha_inicio' => now()->addDays(40),
                'fecha_fin' => now()->addDays(42),
                'modalidad' => 'hibrida',
                'lugar' => 'Centro de Convenciones',
                'enlace_virtual' => 'https://zoom.us/j/123456789',
                'cupo_maximo' => 25,
                'costo' => 0,
                'instructor' => 'Lic. Carlos Martínez',
                'contenido' => 'Módulo 1: Comunicación verbal\nMódulo 2: Comunicación no verbal\nMódulo 3: Comunicación digital'
            ]
        ];

        foreach ($cursos as $cursoData) {
            $curso = Curso::create($cursoData);

            // Crear inscripciones aleatorias
            $miembros = Miembro::take(rand(2, 4))->get();
            foreach ($miembros as $miembro) {
                InscripcionCurso::create([
                    'miembro_id' => $miembro->id,
                    'curso_id' => $curso->id,
                    'fecha_inscripcion' => now()->subDays(rand(1, 10)),
                    'estado' => ['inscrito', 'completado'][rand(0, 1)],
                    'calificacion' => rand(0, 1) == 1 ? rand(70, 100) : null,
                    'observaciones' => 'Inscripción de prueba'
                ]);
            }
        }

        $this->command->info('✅ Cursos creados');
    }

    private function crearNoticias()
    {
        $this->command->info('📰 Creando noticias...');

        $admin = User::where('email', 'admin@cldci.org')->first();
        $tipos = ['asamblea', 'comunicado', 'capacitacion', 'eleccion', 'transaccion'];

        $noticias = [
            [
                'titulo' => 'Asamblea General Ordinaria 2025',
                'contenido' => 'Se convoca a todos los miembros a la Asamblea General Ordinaria que se realizará el próximo mes.',
                'tipo' => 'asamblea',
                'estado' => 'publicada',
                'fecha_publicacion' => now()->subDays(5)
            ],
            [
                'titulo' => 'Nuevo Curso de Liderazgo Comunitario',
                'contenido' => 'Se abre la inscripción para el nuevo curso de liderazgo comunitario que iniciará el próximo mes.',
                'tipo' => 'capacitacion',
                'estado' => 'publicada',
                'fecha_publicacion' => now()->subDays(3)
            ],
            [
                'titulo' => 'Comunicado de la Directiva',
                'contenido' => 'La directiva informa sobre las nuevas medidas implementadas para mejorar la gestión de la organización.',
                'tipo' => 'comunicado',
                'estado' => 'publicada',
                'fecha_publicacion' => now()->subDays(1)
            ]
        ];

        foreach ($noticias as $noticiaData) {
            Noticia::create(array_merge($noticiaData, [
                'autor_id' => $admin->id
            ]));
        }

        $this->command->info('✅ Noticias creadas');
    }

    private function crearTemplatesCarnet()
    {
        $this->command->info('🎫 Creando templates de carnet...');

        $templates = [
            [
                'nombre' => 'Modelo Clásico',
                'descripcion' => 'Diseño clásico y profesional',
                'template_html' => '<div class="carnet-classic">...</div>',
                'template_css' => '.carnet-classic { background: #fff; }',
                'activo' => true
            ],
            [
                'nombre' => 'Modelo Moderno',
                'descripcion' => 'Diseño moderno con colores vibrantes',
                'template_html' => '<div class="carnet-modern">...</div>',
                'template_css' => '.carnet-modern { background: linear-gradient(45deg, #007bff, #28a745); }',
                'activo' => true
            ],
            [
                'nombre' => 'Modelo Corporativo',
                'descripcion' => 'Diseño corporativo elegante',
                'template_html' => '<div class="carnet-corporate">...</div>',
                'template_css' => '.carnet-corporate { background: #f8f9fa; border: 2px solid #dee2e6; }',
                'activo' => true
            ]
        ];

        foreach ($templates as $templateData) {
            CarnetTemplate::create($templateData);
        }

        $this->command->info('✅ Templates de carnet creados');
    }

    private function crearTransaccionesFinancieras()
    {
        $this->command->info('💳 Creando transacciones financieras...');

        $organizacion = Organizacion::where('codigo', 'CLDCI-001')->first();
        $admin = User::where('email', 'admin@cldci.org')->first();

        $transacciones = [
            [
                'tipo' => 'ingreso',
                'concepto' => 'Cuotas de membresía - Enero 2025',
                'monto' => 15000,
                'fecha' => now()->subDays(10),
                'estado' => 'confirmada'
            ],
            [
                'tipo' => 'egreso',
                'concepto' => 'Pago de servicios básicos',
                'monto' => 5000,
                'fecha' => now()->subDays(5),
                'estado' => 'confirmada'
            ],
            [
                'tipo' => 'ingreso',
                'concepto' => 'Donación para eventos',
                'monto' => 3000,
                'fecha' => now()->subDays(2),
                'estado' => 'pendiente'
            ]
        ];

        foreach ($transacciones as $transaccionData) {
            TransaccionFinanciera::create(array_merge($transaccionData, [
                'organizacion_id' => $organizacion->id,
                'created_by' => $admin->id
            ]));
        }

        $this->command->info('✅ Transacciones financieras creadas');
    }
}