<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Organizacion;
use App\Services\Organizaciones\OrganizacionQueryService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrganizacionesApiController extends Controller
{
    public function __construct(
        protected OrganizacionQueryService $organizacionQueryService
    ) {
    }

    /**
     * Get paginated list of organizations
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'buscar',
            'tipo',
            'estado',
        ]);

        $perPage = (int) $request->get('per_page', 20);
        $page = (int) $request->get('page', 1);

        $organizaciones = $this->organizacionQueryService->paginateForApi($filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => $organizaciones['data'],
            'pagination' => $organizaciones['pagination'],
            'filters' => $organizaciones['filters'],
        ]);
    }

    /**
     * Get single organization
     */
    public function show($id): JsonResponse
    {
        try {
            $organizacion = Organizacion::with([
                'miembros' => function ($query) {
                    $query->select('id', 'organizacion_id', 'nombre_completo', 'numero_carnet', 'estado_membresia_id');
                },
                'asambleas' => function ($query) {
                    $query->select('id', 'organizacion_id', 'titulo', 'fecha_asamblea', 'estado');
                },
                'elecciones' => function ($query) {
                    $query->select('id', 'organizacion_id', 'titulo', 'fecha_inicio', 'estado');
                },
                'cursos' => function ($query) {
                    $query->select('id', 'organizacion_id', 'titulo', 'fecha_inicio', 'estado');
                }
            ])->findOrFail($id);

            $estadisticas = [
                'total_miembros' => $organizacion->miembros()->count(),
                'miembros_activos' => $organizacion->miembros()->activos()->count(),
                'miembros_vencidos' => $organizacion->miembros()->vencidos()->count(),
                'total_asambleas' => $organizacion->asambleas()->count(),
                'asambleas_activas' => $organizacion->asambleas()->where('estado', 'convocada')->count(),
                'total_elecciones' => $organizacion->elecciones()->count(),
                'elecciones_activas' => $organizacion->elecciones()->where('estado', 'en_proceso')->count(),
                'total_cursos' => $organizacion->cursos()->count(),
                'cursos_activos' => $organizacion->cursos()->where('estado', 'activo')->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'organizacion' => $organizacion,
                    'estadisticas' => $estadisticas,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Organización no encontrada',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Get organization statistics
     */
    public function estadisticas(): JsonResponse
    {
        try {
            $estadisticas = $this->organizacionQueryService->getEstadisticas();

            return response()->json([
                'success' => true,
                'data' => $estadisticas,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search organizations
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $term = $request->get('q', '');
            
            if (strlen($term) < 2) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                ]);
            }

            $organizaciones = $this->organizacionQueryService->search($term);

            return response()->json([
                'success' => true,
                'data' => $organizaciones,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en la búsqueda',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get organizations by type
     */
    public function porTipo(Request $request): JsonResponse
    {
        try {
            $tipo = $request->get('tipo');
            
            if (!$tipo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipo de organización requerido',
                ], 400);
            }

            $organizaciones = $this->organizacionQueryService->porTipo($tipo);

            return response()->json([
                'success' => true,
                'data' => $organizaciones,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener organizaciones por tipo',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get organizations by state
     */
    public function porEstado(Request $request): JsonResponse
    {
        try {
            $estado = $request->get('estado');
            
            if (!$estado) {
                return response()->json([
                    'success' => false,
                    'message' => 'Estado requerido',
                ], 400);
            }

            $organizaciones = $this->organizacionQueryService->porEstado($estado);

            return response()->json([
                'success' => true,
                'data' => $organizaciones,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener organizaciones por estado',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get organizations with most members
     */
    public function conMasMiembros(Request $request): JsonResponse
    {
        try {
            $limit = (int) $request->get('limit', 10);
            $organizaciones = $this->organizacionQueryService->conMasMiembros($limit);

            return response()->json([
                'success' => true,
                'data' => $organizaciones,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener organizaciones con más miembros',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get organizations without members
     */
    public function sinMiembros(): JsonResponse
    {
        try {
            $organizaciones = $this->organizacionQueryService->sinMiembros();

            return response()->json([
                'success' => true,
                'data' => $organizaciones,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener organizaciones sin miembros',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get organizations created in date range
     */
    public function creadasEnRango(Request $request): JsonResponse
    {
        try {
            $fechaInicio = $request->get('fecha_inicio');
            $fechaFin = $request->get('fecha_fin');
            
            if (!$fechaInicio || !$fechaFin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fechas de inicio y fin requeridas',
                ], 400);
            }

            $organizaciones = $this->organizacionQueryService->creadasEnRango($fechaInicio, $fechaFin);

            return response()->json([
                'success' => true,
                'data' => $organizaciones,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener organizaciones por rango de fechas',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get organizations with upcoming events
     */
    public function conEventosProximos(Request $request): JsonResponse
    {
        try {
            $dias = (int) $request->get('dias', 30);
            $organizaciones = $this->organizacionQueryService->conEventosProximos($dias);

            return response()->json([
                'success' => true,
                'data' => $organizaciones,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener organizaciones con eventos próximos',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get organization performance metrics
     */
    public function metricasRendimiento($id): JsonResponse
    {
        try {
            $metricas = $this->organizacionQueryService->getMetricasRendimiento($id);

            if (empty($metricas)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Organización no encontrada',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $metricas,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener métricas de rendimiento',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get organization members
     */
    public function miembros($id, Request $request): JsonResponse
    {
        try {
            $organizacion = Organizacion::findOrFail($id);
            
            $perPage = (int) $request->get('per_page', 20);
            $filters = $request->only(['estado_membresia', 'tipo_membresia']);

            $query = $organizacion->miembros()->with(['estadoMembresia']);

            // Aplicar filtros
            if (!empty($filters['estado_membresia'])) {
                $query->whereHas('estadoMembresia', function ($q) use ($filters) {
                    $q->where('nombre', $filters['estado_membresia']);
                });
            }

            if (!empty($filters['tipo_membresia'])) {
                $query->where('tipo_membresia', $filters['tipo_membresia']);
            }

            $miembros = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $miembros->items(),
                'pagination' => [
                    'current_page' => $miembros->currentPage(),
                    'per_page' => $miembros->perPage(),
                    'total' => $miembros->total(),
                    'last_page' => $miembros->lastPage(),
                    'from' => $miembros->firstItem(),
                    'to' => $miembros->lastItem(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener miembros de la organización',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

