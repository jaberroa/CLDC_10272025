<?php

namespace App\Http\Controllers;

use App\Models\Directiva;
use App\Models\Miembro;
use App\Models\Organo;
use App\Models\Cargo;
use App\Models\PeriodoDirectiva;
use App\Http\Requests\Directivas\StoreDirectivaRequest;
use App\Http\Requests\Directivas\UpdateDirectivaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DirectivaController extends Controller
{
    /**
     * Display a listing of directivas.
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'buscar',
            'estado',
            'organo_id',
            'cargo_id',
            'periodo_directiva',
        ]);

        $query = Directiva::with(['miembro', 'organo', 'cargo']);

        // Aplicar filtros
        if (!empty($filters['buscar'])) {
            $query->whereHas('miembro', function ($q) use ($filters) {
                $q->where('nombre_completo', 'like', '%' . $filters['buscar'] . '%')
                  ->orWhere('cedula', 'like', '%' . $filters['buscar'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['buscar'] . '%');
            });
        }

        if (!empty($filters['estado'])) {
            $query->where('estado', $filters['estado']);
        }

        if (!empty($filters['organo_id'])) {
            $query->where('organo_id', $filters['organo_id']);
        }

        if (!empty($filters['cargo_id'])) {
            $query->where('cargo_id', $filters['cargo_id']);
        }

        if (!empty($filters['periodo_directiva'])) {
            $query->where('periodo_directiva', 'like', '%' . $filters['periodo_directiva'] . '%');
        }

        // Ordenar por fecha de inicio descendente
        $query->orderBy('fecha_inicio', 'desc');

        $directivas = $query->paginate(25);

        // Estadísticas
        $estadisticas = [
            'total' => Directiva::count(),
            'activos' => Directiva::activos()->count(),
            'inactivos' => Directiva::inactivos()->count(),
            'vigentes' => Directiva::vigentes()->count(),
            'vencidos' => Directiva::vencidos()->count(),
        ];

        // Datos para filtros
        $organos = Organo::activos()->get();
        $cargos = Cargo::activos()->get();

        return view('directivas.index', compact(
            'directivas',
            'estadisticas',
            'organos',
            'cargos',
            'filters'
        ));
    }

    /**
     * Show the form for creating a new directiva.
     */
    public function create()
    {
        $miembros = Miembro::activos()->get();
        $organos = Organo::activos()->get();
        $cargos = Cargo::activos()->get();

        return view('directivas.create', compact(
            'miembros',
            'organos',
            'cargos'
        ));
    }

    /**
     * Store a newly created directiva.
     */
    public function store(StoreDirectivaRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $data['created_by'] = Auth::id();

            // Verificar si el miembro ya tiene un cargo activo en el mismo órgano
            $conflicto = Directiva::where('miembro_id', $data['miembro_id'])
                                 ->where('organo_id', $data['organo_id'])
                                 ->where('estado', 'activo')
                                 ->where(function ($q) use ($data) {
                                     $q->whereNull('fecha_fin')
                                       ->orWhere('fecha_fin', '>=', $data['fecha_inicio']);
                                 })
                                 ->exists();

            if ($conflicto) {
                return back()->withErrors([
                    'miembro_id' => 'El miembro ya tiene un cargo activo en este órgano durante el período especificado.'
                ])->withInput();
            }

            $directiva = Directiva::create($data);

            DB::commit();

            return redirect()->route('directivas.index')
                           ->with('success', 'Directiva creada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear la directiva: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Display the specified directiva.
     */
    public function show(Directiva $directiva)
    {
        $directiva->load(['miembro', 'organo', 'cargo', 'creadoPor', 'actualizadoPor']);

        return view('directivas.show', compact('directiva'));
    }

    /**
     * Show the form for editing the specified directiva.
     */
    public function edit(Directiva $directiva)
    {
        $miembros = Miembro::activos()->get();
        $organos = Organo::activos()->get();
        $cargos = Cargo::activos()->get();

        return view('directivas.edit', compact(
            'directiva',
            'miembros',
            'organos',
            'cargos'
        ));
    }

    /**
     * Update the specified directiva.
     */
    public function update(UpdateDirectivaRequest $request, Directiva $directiva)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $data['updated_by'] = Auth::id();

            // Verificar conflicto solo si cambió el miembro, órgano o fechas
            if ($data['miembro_id'] != $directiva->miembro_id || 
                $data['organo_id'] != $directiva->organo_id ||
                $data['fecha_inicio'] != $directiva->fecha_inicio) {
                
                $conflicto = Directiva::where('miembro_id', $data['miembro_id'])
                                     ->where('organo_id', $data['organo_id'])
                                     ->where('estado', 'activo')
                                     ->where('id', '!=', $directiva->id)
                                     ->where(function ($q) use ($data) {
                                         $q->whereNull('fecha_fin')
                                           ->orWhere('fecha_fin', '>=', $data['fecha_inicio']);
                                     })
                                     ->exists();

                if ($conflicto) {
                    return back()->withErrors([
                        'miembro_id' => 'El miembro ya tiene un cargo activo en este órgano durante el período especificado.'
                    ])->withInput();
                }
            }

            $directiva->update($data);

            DB::commit();

            return redirect()->route('directivas.index')
                           ->with('success', 'Directiva actualizada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar la directiva: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Remove the specified directiva.
     */
    public function destroy(Directiva $directiva)
    {
        try {
            $directiva->delete();

            return response()->json([
                'success' => true,
                'message' => 'Directiva eliminada exitosamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la directiva: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete directivas.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:directivas,id'
        ]);

        try {
            $deleted = Directiva::whereIn('id', $request->ids)->delete();

            return response()->json([
                'success' => true,
                'message' => "Se eliminaron {$deleted} directivas exitosamente."
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar las directivas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Activate directiva.
     */
    public function activate(Directiva $directiva)
    {
        try {
            if (!$directiva->puedeSerActivo()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede activar la directiva debido a conflictos con otros cargos.'
                ], 400);
            }

            $directiva->activar();

            return response()->json([
                'success' => true,
                'message' => 'Directiva activada exitosamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al activar la directiva: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Deactivate directiva.
     */
    public function deactivate(Directiva $directiva)
    {
        try {
            $directiva->desactivar();

            return response()->json([
                'success' => true,
                'message' => 'Directiva desactivada exitosamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al desactivar la directiva: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Suspend directiva.
     */
    public function suspend(Directiva $directiva)
    {
        try {
            $directiva->suspender();

            return response()->json([
                'success' => true,
                'message' => 'Directiva suspendida exitosamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al suspender la directiva: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Finish directiva.
     */
    public function finish(Directiva $directiva, Request $request)
    {
        $request->validate([
            'fecha_fin' => 'nullable|date|after_or_equal:today'
        ]);

        try {
            $directiva->finalizar($request->fecha_fin);

            return response()->json([
                'success' => true,
                'message' => 'Directiva finalizada exitosamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al finalizar la directiva: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Renew directiva.
     */
    public function renew(Directiva $directiva, Request $request)
    {
        $request->validate([
            'fecha_fin' => 'required|date|after:today'
        ]);

        try {
            $directiva->renovar($request->fecha_fin);

            return response()->json([
                'success' => true,
                'message' => 'Directiva renovada exitosamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al renovar la directiva: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get directivas by organo.
     */
    public function porOrgano(Organo $organo)
    {
        $directivas = Directiva::porOrgano($organo->id)
                              ->with(['miembro', 'cargo'])
                              ->get();

        return response()->json($directivas);
    }

    /**
     * Get directivas by cargo.
     */
    public function porCargo(Cargo $cargo)
    {
        $directivas = Directiva::porCargo($cargo->id)
                              ->with(['miembro', 'organo'])
                              ->get();

        return response()->json($directivas);
    }

    /**
     * Get active directivas.
     */
    public function activas()
    {
        $directivas = Directiva::directivosActivos();

        return response()->json($directivas);
    }

    /**
     * Get upcoming expirations.
     */
    public function proximosVencimientos(Request $request)
    {
        $dias = $request->get('dias', 30);
        $directivas = Directiva::proximosVencimientos($dias);

        return response()->json($directivas);
    }

    /**
     * Export directivas.
     */
    public function export(Request $request)
    {
        // TODO: Implementar exportación de directivas
        return response()->json(['message' => 'Función de exportación pendiente']);
    }
}