<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organizacion;
use App\Models\Miembro;
use App\Models\Asamblea;
use App\Models\Capacitacion;
use App\Models\Eleccion;
use App\Models\TransaccionFinanciera;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        // Obtener estadísticas en tiempo real
        $estadisticas = [
            'total_miembros' => Miembro::count(),
            'miembros_activos' => Miembro::whereHas('estadoMembresia', function($query) {
                $query->where('nombre', 'activa');
            })->count(),
            'organizaciones_activas' => Organizacion::whereHas('estadoAdecuacion', function($query) {
                $query->where('nombre', 'aprobada');
            })->count(),
            'asambleas_programadas' => 0, // Tabla no existe aún
            'capacitaciones_activas' => 0, // Tabla no existe aún
            'elecciones_activas' => 0, // Tabla no existe aún
            'ingresos_mes' => 0, // Tabla no existe aún
            'gastos_mes' => 0, // Tabla no existe aún
        ];

        // Obtener datos para gráficos (simplificado)
        $miembrosPorTipo = collect([
            ['tipo' => 'activa', 'cantidad' => Miembro::count()],
            ['tipo' => 'suspendida', 'cantidad' => 0],
            ['tipo' => 'inactiva', 'cantidad' => 0]
        ]);

        $organizacionesPorTipo = collect([
            ['tipo' => 'nacional', 'cantidad' => Organizacion::count()],
            ['tipo' => 'seccional', 'cantidad' => 0],
            ['tipo' => 'seccional_internacional', 'cantidad' => 0]
        ]);

        $transaccionesRecientes = collect(); // Tabla no existe aún
        $asambleasRecientes = collect(); // Tabla no existe aún

        // Obtener datos de asistencias (simplificado)
        $asistenciasData = [
            'miembros_activos_porcentaje' => $estadisticas['total_miembros'] > 0 ? 
                round(($estadisticas['miembros_activos'] / $estadisticas['total_miembros']) * 100, 1) : 0,
            'fundadores_porcentaje' => 25.0, // Valor simulado
            'estudiantes_porcentaje' => 35.0, // Valor simulado
        ];

        // Obtener próximos eventos (simplificado)
        $proximosEventos = collect([
            [
                'tipo' => 'asamblea',
                'titulo' => 'Asamblea General Extraordinaria',
                'fecha' => now()->addDays(15),
                'lugar' => 'Sede Nacional CLDCI',
                'modalidad' => 'presencial',
                'icono' => 'ri-calendar-line',
                'color' => 'info'
            ],
            [
                'tipo' => 'eleccion',
                'titulo' => 'Elecciones Directiva 2025',
                'fecha' => now()->addDays(30),
                'lugar' => 'Virtual',
                'modalidad' => 'virtual',
                'icono' => 'ri-government-line',
                'color' => 'success'
            ]
        ]);

        // Obtener miembros más activos en asambleas (Top Performers) - Versión simplificada
        $topPerformersAsambleas = Miembro::with(['organizacion'])
            ->limit(5)
            ->get()
            ->map(function ($miembro) {
                // Datos simulados para demostración
                $asistencias = rand(1, 10);
                $porcentajeAsistencia = rand(60, 100);
                
                // Determinar nivel de reconocimiento
                $nivel = 'Miembro Activo';
                $badgeColor = 'primary';
                if ($porcentajeAsistencia >= 90) {
                    $nivel = 'Miembro Ejemplar';
                    $badgeColor = 'success';
                } elseif ($porcentajeAsistencia >= 75) {
                    $nivel = 'Miembro Destacado';
                    $badgeColor = 'info';
                }

                // Extraer solo el nombre del miembro
                $nombreSolo = $miembro->nombre_completo;
                if (strpos($nombreSolo, ' de CLDCI Seccional') !== false) {
                    $nombreSolo = explode(' de CLDCI Seccional', $nombreSolo)[0];
                }
                
                // Extraer solo el nombre de la seccional
                $seccional = $miembro->organizacion->nombre ?? 'Sin organización';
                if (strpos($seccional, 'CLDCI Seccional ') !== false) {
                    $seccional = str_replace('CLDCI Seccional ', '', $seccional);
                }

                return [
                    'id' => $miembro->id,
                    'nombre' => $miembro->nombre_completo,
                    'nombre_solo' => $nombreSolo,
                    'organizacion' => $miembro->organizacion->nombre ?? 'Sin organización',
                    'seccional' => $seccional,
                    'asistencias' => $asistencias,
                    'porcentaje' => $porcentajeAsistencia,
                    'nivel' => $nivel,
                    'badge_color' => $badgeColor,
                    'foto' => $miembro->foto_url ?? 'assets/images/avatar/avatar-1.jpg'
                ];
            });

        // Obtener miembros más activos en capacitaciones (Top Performers) - Versión simplificada
        $topPerformersCapacitaciones = Miembro::with(['organizacion'])
            ->limit(5)
            ->get()
            ->map(function ($miembro) {
                // Datos simulados para demostración
                $cursos = rand(1, 8);
                $porcentajeCapacitacion = rand(50, 95);
                
                // Determinar nivel de reconocimiento
                $nivel = 'Estudiante Activo';
                $badgeColor = 'primary';
                if ($porcentajeCapacitacion >= 80) {
                    $nivel = 'Estudiante Ejemplar';
                    $badgeColor = 'success';
                } elseif ($porcentajeCapacitacion >= 60) {
                    $nivel = 'Estudiante Destacado';
                    $badgeColor = 'info';
                }

                // Extraer solo el nombre del miembro
                $nombreSolo = $miembro->nombre_completo;
                if (strpos($nombreSolo, ' de CLDCI Seccional') !== false) {
                    $nombreSolo = explode(' de CLDCI Seccional', $nombreSolo)[0];
                }
                
                // Extraer solo el nombre de la seccional
                $seccional = $miembro->organizacion->nombre ?? 'Sin organización';
                if (strpos($seccional, 'CLDCI Seccional ') !== false) {
                    $seccional = str_replace('CLDCI Seccional ', '', $seccional);
                }

                return [
                    'id' => $miembro->id,
                    'nombre' => $miembro->nombre_completo,
                    'nombre_solo' => $nombreSolo,
                    'organizacion' => $miembro->organizacion->nombre ?? 'Sin organización',
                    'seccional' => $seccional,
                    'asistencias' => $cursos,
                    'porcentaje' => $porcentajeCapacitacion,
                    'nivel' => $nivel,
                    'badge_color' => $badgeColor,
                    'foto' => $miembro->foto_url ?? 'assets/images/avatar/avatar-1.jpg'
                ];
            });

        // Obtener miembros más activos en elecciones (Top Performers) - Versión simplificada
        $topPerformersElecciones = Miembro::with(['organizacion'])
            ->limit(5)
            ->get()
            ->map(function ($miembro) {
                // Datos simulados para demostración
                $elecciones = rand(1, 5);
                $porcentajeEleccion = rand(70, 100);
                
                // Determinar nivel de reconocimiento
                $nivel = 'Elector Activo';
                $badgeColor = 'primary';
                if ($porcentajeEleccion >= 100) {
                    $nivel = 'Elector Ejemplar';
                    $badgeColor = 'success';
                } elseif ($porcentajeEleccion >= 75) {
                    $nivel = 'Elector Destacado';
                    $badgeColor = 'info';
                }

                // Extraer solo el nombre del miembro
                $nombreSolo = $miembro->nombre_completo;
                if (strpos($nombreSolo, ' de CLDCI Seccional') !== false) {
                    $nombreSolo = explode(' de CLDCI Seccional', $nombreSolo)[0];
                }
                
                // Extraer solo el nombre de la seccional
                $seccional = $miembro->organizacion->nombre ?? 'Sin organización';
                if (strpos($seccional, 'CLDCI Seccional ') !== false) {
                    $seccional = str_replace('CLDCI Seccional ', '', $seccional);
                }

                return [
                    'id' => $miembro->id,
                    'nombre' => $miembro->nombre_completo,
                    'nombre_solo' => $nombreSolo,
                    'organizacion' => $miembro->organizacion->nombre ?? 'Sin organización',
                    'seccional' => $seccional,
                    'asistencias' => $elecciones,
                    'porcentaje' => $porcentajeEleccion,
                    'nivel' => $nivel,
                    'badge_color' => $badgeColor,
                    'foto' => $miembro->foto_url ?? 'assets/images/avatar/avatar-1.jpg'
                ];
            });

            // Obtener noticias para Notice Board (3 más importantes) - Versión simplificada
            $noticiasImportantes = collect([
                [
                    'tipo' => 'asamblea',
                    'titulo' => 'Asamblea General Extraordinaria',
                    'descripcion' => 'Convocatoria oficial de asamblea',
                    'fecha' => now()->addDays(15),
                    'lugar' => 'Sede Nacional CLDCI',
                    'modalidad' => 'presencial',
                    'icono' => 'ri-calendar-line',
                    'color' => 'info',
                    'prioridad' => 1
                ],
                [
                    'tipo' => 'comunicado',
                    'titulo' => 'Nueva Política de Membresía',
                    'descripcion' => 'Comunicado oficial de la directiva',
                    'fecha' => now()->subDays(5),
                    'lugar' => 'Oficina Nacional',
                    'modalidad' => 'oficial',
                    'icono' => 'ri-megaphone-line',
                    'color' => 'warning',
                    'prioridad' => 2
                ],
                [
                    'tipo' => 'capacitacion',
                    'titulo' => 'Curso de Locución Profesional',
                    'descripcion' => 'Nueva capacitación disponible',
                    'fecha' => now()->addDays(20),
                    'lugar' => 'Estudio de Grabación CLDCI',
                    'modalidad' => 'presencial',
                    'icono' => 'ri-graduation-cap-line',
                    'color' => 'success',
                    'prioridad' => 3
                ]
            ]);

            return view('dashboard', compact(
                'estadisticas',
                'miembrosPorTipo',
                'organizacionesPorTipo',
                'transaccionesRecientes',
                'asambleasRecientes',
                'asistenciasData',
                'proximosEventos',
                'topPerformersAsambleas',
                'topPerformersCapacitaciones',
                'topPerformersElecciones',
                'noticiasImportantes'
            ));
    }

    /**
     * Obtener estadísticas específicas por organización
     */
    public function estadisticasOrganizacion($organizacionId)
    {
        $organizacion = Organizacion::findOrFail($organizacionId);
        
        $estadisticas = [
            'miembros_activos' => $organizacion->miembros()->activos()->count(),
            'asambleas_programadas' => $organizacion->asambleas()->programadas()->count(),
            'cursos_activos' => $organizacion->cursos()->activos()->count(),
            'elecciones_activas' => $organizacion->elecciones()->activas()->count(),
            'presupuesto_total' => $organizacion->presupuestos()->where('activo', true)->sum('monto_presupuestado'),
            'presupuesto_ejecutado' => $organizacion->presupuestos()->where('activo', true)->sum('monto_ejecutado'),
        ];

        return response()->json($estadisticas);
    }

    /**
     * Obtener organizaciones del usuario actual
     */
    private function getOrganizacionesUsuario()
    {
        // Por ahora retornar todas, después implementar lógica de roles
        return Organizacion::activas()
            ->with(['miembros' => function ($query) {
                $query->activos();
            }])
            ->get()
            ->map(function ($org) {
                return [
                    'id' => $org->id,
                    'nombre' => $org->nombre,
                    'tipo' => $org->tipo,
                    'miembros_count' => $org->miembros->count(),
                    'estadisticas' => $org->estadisticas,
                ];
            });
    }

    /**
     * Obtener etiqueta legible para tipo de organización
     */
    private function getTipoOrganizacionLabel($tipo)
    {
        $labels = [
            'nacional' => 'Órgano Nacional',
            'seccional' => 'Seccionales Provinciales',
            'seccional_internacional' => 'Seccionales Internacionales',
            'diaspora' => 'Diáspora',
        ];

        return $labels[$tipo] ?? ucfirst($tipo);
    }

    /**
     * Obtener ejemplos para tipo de organización
     */
    private function getEjemplosTipo($tipo)
    {
        $ejemplos = [
            'nacional' => 'CLDCI Nacional, Consejo Directivo Nacional',
            'seccional' => 'CLDCI Santiago, CLDCI Santo Domingo, CLDCI La Vega',
            'seccional_internacional' => 'CLDCI Estados Unidos, CLDCI España, CLDCI Italia',
            'diaspora' => 'Representaciones en el exterior',
        ];

        return $ejemplos[$tipo] ?? '';
    }

    /**
     * Obtener estadísticas en tiempo real para API
     */
    public function getStats()
    {
        try {
            $stats = [
                'miembros_activos' => Miembro::activos()->count(),
                'organizaciones' => Organizacion::count(),
                'seccionales_provinciales' => Organizacion::seccionales()->count(),
                'seccionales_internacionales' => Organizacion::seccionalesInternacionales()->count(),
                'asambleas_programadas' => Asamblea::programadas()->count(),
                'cursos_activos' => Curso::activos()->count(),
                'elecciones_activas' => Eleccion::activas()->count(),
                'miembros_nuevos_mes' => Miembro::where('created_at', '>=', now()->subMonth())->count(),
                'timestamp' => now()->toISOString()
            ];

            return response()->json($stats);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener estadísticas',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener datos para gráficos del dashboard
     */
    public function datosGraficos()
    {
        try {
            // Gráfico de miembros por tipo de membresía
            $miembrosPorTipo = Miembro::select('tipo_membresia', DB::raw('count(*) as cantidad'))
                ->groupBy('tipo_membresia')
                ->get();

            // Gráfico de asambleas por modalidad
            $asambleasPorModalidad = Asamblea::select('modalidad', DB::raw('count(*) as cantidad'))
                ->groupBy('modalidad')
                ->get();

            // Gráfico de cursos por modalidad
            $cursosPorModalidad = Curso::select('modalidad', DB::raw('count(*) as cantidad'))
                ->groupBy('modalidad')
                ->get();

            // Gráfico de distribución por tipo de organización
            $distribucionOrganizaciones = Organizacion::select('tipo', DB::raw('count(*) as cantidad'))
                ->groupBy('tipo')
                ->get();

            return response()->json([
                'miembros_por_tipo' => $miembrosPorTipo,
                'asambleas_por_modalidad' => $asambleasPorModalidad,
                'cursos_por_modalidad' => $cursosPorModalidad,
                'distribucion_organizaciones' => $distribucionOrganizaciones,
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener datos de gráficos',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
