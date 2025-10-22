<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Miembro;
use App\Models\Organizacion;
use App\Models\Asamblea;
use App\Models\Capacitacion;
use App\Models\Eleccion;
use App\Models\TransaccionFinanciera;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardApiController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function estadisticas(): JsonResponse
    {
        $estadisticas = [
            'total_miembros' => Miembro::count(),
            'miembros_activos' => Miembro::whereHas('estadoMembresia', function($query) {
                $query->where('nombre', 'activa');
            })->count(),
            'organizaciones_activas' => Organizacion::whereHas('estadoAdecuacion', function($query) {
                $query->where('nombre', 'aprobada');
            })->count(),
            'asambleas_programadas' => Asamblea::where('estado', 'convocada')->count(),
            'capacitaciones_activas' => Capacitacion::where('estado', 'programada')->count(),
            'elecciones_activas' => Eleccion::where('estado', 'programada')->count(),
            'ingresos_mes' => TransaccionFinanciera::where('tipo', 'ingreso')
                ->whereMonth('fecha', now()->month)
                ->sum('monto'),
            'gastos_mes' => TransaccionFinanciera::where('tipo', 'gasto')
                ->whereMonth('fecha', now()->month)
                ->sum('monto'),
        ];

        return response()->json([
            'success' => true,
            'data' => $estadisticas
        ]);
    }

    /**
     * Get chart data for members by type
     */
    public function miembrosPorTipo(): JsonResponse
    {
        $data = Miembro::join('estados_membresia', 'miembros.estado_membresia_id', '=', 'estados_membresia.id')
            ->select('estados_membresia.nombre as tipo', DB::raw('count(*) as cantidad'))
            ->groupBy('estados_membresia.nombre')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Get chart data for organizations by type
     */
    public function organizacionesPorTipo(): JsonResponse
    {
        $data = Organizacion::join('tipos_organizacion', 'organizaciones.tipo_organizacion_id', '=', 'tipos_organizacion.id')
            ->select('tipos_organizacion.nombre as tipo', DB::raw('count(*) as cantidad'))
            ->groupBy('tipos_organizacion.nombre')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Get recent financial transactions
     */
    public function transaccionesRecientes(): JsonResponse
    {
        $transacciones = TransaccionFinanciera::with('organizacion')
            ->orderBy('fecha', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $transacciones
        ]);
    }

    /**
     * Get upcoming assemblies
     */
    public function asambleasProximas(): JsonResponse
    {
        $asambleas = Asamblea::with('organizacion')
            ->where('fecha_asamblea', '>=', now())
            ->where('estado', 'convocada')
            ->orderBy('fecha_asamblea', 'asc')
            ->limit(3)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $asambleas
        ]);
    }

    /**
     * Get upcoming elections
     */
    public function eleccionesProximas(): JsonResponse
    {
        $elecciones = Eleccion::with('padron.organizacion')
            ->where('fecha_inicio', '>=', now())
            ->where('estado', 'programada')
            ->orderBy('fecha_inicio', 'asc')
            ->limit(3)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $elecciones
        ]);
    }

    /**
     * Get most active members
     */
    public function miembrosActivos(Request $request): JsonResponse
    {
        $tipo = $request->get('tipo', 'asambleas');
        $limit = $request->get('limit', 5);

        $query = Miembro::with(['organizacion', 'estadoMembresia']);

        switch ($tipo) {
            case 'asambleas':
                $query->withCount(['asistenciaAsambleas as participaciones' => function($q) {
                    $q->where('presente', true);
                }]);
                break;
            case 'capacitaciones':
                $query->withCount(['inscripcionesCapacitacion as participaciones' => function($q) {
                    $q->where('asistio', true);
                }]);
                break;
            case 'elecciones':
                $query->withCount(['votos as participaciones']);
                break;
        }

        $miembros = $query->orderBy('participaciones', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $miembros
        ]);
    }

    /**
     * Get financial summary
     */
    public function resumenFinanciero(): JsonResponse
    {
        $resumen = [
            'ingresos_mes_actual' => TransaccionFinanciera::where('tipo', 'ingreso')
                ->whereMonth('fecha', now()->month)
                ->whereYear('fecha', now()->year)
                ->sum('monto'),
            'gastos_mes_actual' => TransaccionFinanciera::where('tipo', 'gasto')
                ->whereMonth('fecha', now()->month)
                ->whereYear('fecha', now()->year)
                ->sum('monto'),
            'ingresos_mes_anterior' => TransaccionFinanciera::where('tipo', 'ingreso')
                ->whereMonth('fecha', now()->subMonth()->month)
                ->whereYear('fecha', now()->subMonth()->year)
                ->sum('monto'),
            'gastos_mes_anterior' => TransaccionFinanciera::where('tipo', 'gasto')
                ->whereMonth('fecha', now()->subMonth()->month)
                ->whereYear('fecha', now()->subMonth()->year)
                ->sum('monto'),
        ];

        $resumen['balance_mes_actual'] = $resumen['ingresos_mes_actual'] - $resumen['gastos_mes_actual'];
        $resumen['balance_mes_anterior'] = $resumen['ingresos_mes_anterior'] - $resumen['gastos_mes_anterior'];

        return response()->json([
            'success' => true,
            'data' => $resumen
        ]);
    }
}


