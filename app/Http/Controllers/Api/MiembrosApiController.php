<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Miembro;
use App\Models\Organizacion;
use App\Models\EstadoMembresia;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MiembrosApiController extends Controller
{
    /**
     * Get members list with filters
     */
    public function index(Request $request): JsonResponse
    {
        $query = Miembro::with(['organizacion', 'estadoMembresia']);

        // Apply filters
        if ($request->filled('buscar')) {
            $search = $request->get('buscar');
            $query->where(function($q) use ($search) {
                $q->where('nombre_completo', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('cedula', 'like', "%{$search}%")
                  ->orWhere('numero_carnet', 'like', "%{$search}%");
            });
        }

        if ($request->filled('estado_membresia_id')) {
            $query->where('estado_membresia_id', $request->get('estado_membresia_id'));
        }

        if ($request->filled('organizacion_id')) {
            $query->where('organizacion_id', $request->get('organizacion_id'));
        }

        $perPage = $request->get('per_page', 15);
        $miembros = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $miembros->items(),
            'pagination' => [
                'current_page' => $miembros->currentPage(),
                'last_page' => $miembros->lastPage(),
                'per_page' => $miembros->perPage(),
                'total' => $miembros->total(),
                'from' => $miembros->firstItem(),
                'to' => $miembros->lastItem(),
            ],
            'links' => [
                'first' => $miembros->url(1),
                'last' => $miembros->url($miembros->lastPage()),
                'prev' => $miembros->previousPageUrl(),
                'next' => $miembros->nextPageUrl(),
            ]
        ]);
    }

    /**
     * Get member details
     */
    public function show($id): JsonResponse
    {
        $miembro = Miembro::with(['organizacion', 'estadoMembresia'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $miembro
        ]);
    }

    /**
     * Get member statistics
     */
    public function estadisticas(): JsonResponse
    {
        $estadisticas = [
            'total_miembros' => Miembro::count(),
            'miembros_activos' => Miembro::whereHas('estadoMembresia', function($query) {
                $query->where('nombre', 'activa');
            })->count(),
            'miembros_por_organizacion' => Miembro::join('organizaciones', 'miembros.organizacion_id', '=', 'organizaciones.id')
                ->select('organizaciones.nombre', DB::raw('count(*) as total'))
                ->groupBy('organizaciones.id', 'organizaciones.nombre')
                ->get(),
            'miembros_por_estado' => Miembro::join('estados_membresia', 'miembros.estado_membresia_id', '=', 'estados_membresia.id')
                ->select('estados_membresia.nombre', DB::raw('count(*) as total'))
                ->groupBy('estados_membresia.id', 'estados_membresia.nombre')
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $estadisticas
        ]);
    }

    /**
     * Search members
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        $miembros = Miembro::with(['organizacion', 'estadoMembresia'])
            ->where(function($q) use ($query) {
                $q->where('nombre_completo', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('cedula', 'like', "%{$query}%")
                  ->orWhere('numero_carnet', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $miembros
        ]);
    }

    /**
     * Get organizations for filter
     */
    public function organizaciones(): JsonResponse
    {
        $organizaciones = Organizacion::activas()
            ->select('id', 'nombre', 'codigo')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $organizaciones
        ]);
    }

    /**
     * Get membership states for filter
     */
    public function estadosMembresia(): JsonResponse
    {
        $estados = EstadoMembresia::select('id', 'nombre', 'descripcion')->get();

        return response()->json([
            'success' => true,
            'data' => $estados
        ]);
    }
}


