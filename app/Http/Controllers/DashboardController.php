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
            'miembros_activos' => Miembro::where('estado_membresia', 'activa')->count(),
            'organizaciones_activas' => Organizacion::where('estado_adecuacion', 'aprobada')->count(),
            'asambleas_programadas' => Asamblea::where('estado', 'convocada')->count(),
            'capacitaciones_activas' => Capacitacion::where('estado', 'programada')->count(),
            'elecciones_activas' => Eleccion::where('estado', 'preparacion')->count(),
            'ingresos_mes' => TransaccionFinanciera::where('tipo', 'ingreso')
                ->whereMonth('fecha', now()->month)
                ->sum('monto'),
            'gastos_mes' => TransaccionFinanciera::where('tipo', 'gasto')
                ->whereMonth('fecha', now()->month)
                ->sum('monto'),
        ];

        // Obtener datos para gráficos
        $miembrosPorTipo = Miembro::select('tipo_membresia', DB::raw('count(*) as cantidad'))
            ->groupBy('tipo_membresia')
            ->get();

        $organizacionesPorTipo = Organizacion::select('tipo', DB::raw('count(*) as cantidad'))
            ->groupBy('tipo')
            ->get();

        $transaccionesRecientes = TransaccionFinanciera::with('organizacion')
            ->orderBy('fecha', 'desc')
            ->limit(5)
            ->get();

        $asambleasRecientes = Asamblea::with('organizacion')
            ->orderBy('fecha_asamblea', 'desc')
            ->limit(5)
            ->get();

        // Obtener datos de asistencias
        $asistenciasData = [
            'miembros_activos_porcentaje' => $estadisticas['total_miembros'] > 0 ? 
                round(($estadisticas['miembros_activos'] / $estadisticas['total_miembros']) * 100, 1) : 0,
            'fundadores_porcentaje' => $estadisticas['total_miembros'] > 0 ? 
                round((($miembrosPorTipo->where('tipo_membresia', 'fundador')->first()->cantidad ?? 0) / $estadisticas['total_miembros']) * 100, 1) : 0,
            'estudiantes_porcentaje' => $estadisticas['total_miembros'] > 0 ? 
                round((($miembrosPorTipo->where('tipo_membresia', 'estudiante')->first()->cantidad ?? 0) / $estadisticas['total_miembros']) * 100, 1) : 0,
        ];

        // Obtener próximos eventos (asambleas y elecciones)
        $proximosEventos = collect();
        
        // Próximas asambleas (máximo 3)
        $proximasAsambleas = Asamblea::where('fecha_asamblea', '>=', now())
            ->whereIn('estado', ['convocada', 'programada'])
            ->orderBy('fecha_asamblea', 'asc')
            ->limit(3)
            ->get()
            ->map(function ($asamblea) {
                return [
                    'tipo' => 'asamblea',
                    'titulo' => $asamblea->titulo,
                    'fecha' => $asamblea->fecha_asamblea,
                    'lugar' => $asamblea->lugar ?? 'Por definir',
                    'modalidad' => $asamblea->modalidad,
                    'icono' => 'ri-calendar-line',
                    'color' => 'info'
                ];
            });

        // Próximas elecciones (máximo 2)
        $proximasElecciones = Eleccion::where('fecha_inicio', '>=', now())
            ->whereIn('estado', ['preparacion', 'activa'])
            ->orderBy('fecha_inicio', 'asc')
            ->limit(2)
            ->get()
            ->map(function ($eleccion) {
                return [
                    'tipo' => 'eleccion',
                    'titulo' => $eleccion->titulo,
                    'fecha' => $eleccion->fecha_inicio,
                    'lugar' => 'Virtual',
                    'modalidad' => 'virtual',
                    'icono' => 'ri-government-line',
                    'color' => 'success'
                ];
            });

        // Combinar y ordenar por fecha
        $proximosEventos = $proximasAsambleas->concat($proximasElecciones)
            ->sortBy('fecha')
            ->take(3);

        // Obtener miembros más activos en asambleas (Top Performers)
        $topPerformersAsambleas = Miembro::withCount(['asistenciaAsambleas' => function($query) {
                $query->where('presente', true);
            }])
            ->with(['organizacion'])
            ->having('asistencia_asambleas_count', '>', 0)
            ->orderBy('asistencia_asambleas_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($miembro) {
                // Calcular porcentaje de asistencia
                $totalAsambleas = \App\Models\Asamblea::where('fecha_asamblea', '<=', now())->count();
                $porcentajeAsistencia = $totalAsambleas > 0 ? 
                    round(($miembro->asistencia_asambleas_count / $totalAsambleas) * 100, 1) : 0;
                
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

                // Extraer solo el nombre del miembro (sin "Miembro X de CLDCI Seccional...")
                $nombreSolo = $miembro->nombre_completo;
                if (strpos($nombreSolo, ' de CLDCI Seccional') !== false) {
                    $nombreSolo = explode(' de CLDCI Seccional', $nombreSolo)[0];
                }
                
                // Extraer solo el nombre de la seccional (sin "CLDCI Seccional")
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
                    'asistencias' => $miembro->asistencia_asambleas_count,
                    'porcentaje' => $porcentajeAsistencia,
                    'nivel' => $nivel,
                    'badge_color' => $badgeColor,
                    'foto' => $miembro->foto_url ?? 'assets/images/avatar/avatar-1.jpg'
                ];
            });

        // Obtener miembros más activos en capacitaciones (Top Performers)
        $topPerformersCapacitaciones = Miembro::withCount(['inscripcionesCapacitacion' => function($query) {
                $query->where('asistio', true);
            }])
            ->with(['organizacion'])
            ->having('inscripciones_capacitacion_count', '>', 0)
            ->orderBy('inscripciones_capacitacion_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($miembro) {
                // Calcular porcentaje de participación en capacitaciones
                $totalCapacitaciones = \App\Models\Capacitacion::where('fecha_inicio', '<=', now())->count();
                $porcentajeCapacitacion = $totalCapacitaciones > 0 ? 
                    round(($miembro->inscripciones_capacitacion_count / $totalCapacitaciones) * 100, 1) : 0;
                
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
                    'asistencias' => $miembro->inscripciones_capacitacion_count,
                    'porcentaje' => $porcentajeCapacitacion,
                    'nivel' => $nivel,
                    'badge_color' => $badgeColor,
                    'foto' => $miembro->foto_url ?? 'assets/images/avatar/avatar-1.jpg'
                ];
            });

        // Obtener miembros más activos en elecciones (Top Performers)
        $topPerformersElecciones = Miembro::withCount(['electores' => function($query) {
                $query->where('elegible', true);
            }])
            ->with(['organizacion'])
            ->having('electores_count', '>', 0)
            ->orderBy('electores_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($miembro) {
                // Calcular porcentaje de participación electoral
                $totalElecciones = \App\Models\Eleccion::where('fecha_inicio', '<=', now())->count();
                $porcentajeEleccion = $totalElecciones > 0 ? 
                    round(($miembro->electores_count / $totalElecciones) * 100, 1) : 0;
                
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
                    'asistencias' => $miembro->electores_count,
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
