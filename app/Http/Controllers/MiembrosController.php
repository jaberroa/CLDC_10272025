<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Miembro;
use App\Models\Organizacion;
use Illuminate\Support\Facades\DB;

class MiembrosController extends Controller
{
    /**
     * Display a listing of members.
     */
    public function index(Request $request)
    {
        $query = Miembro::with('organizacion');

        // Filtros
        if ($request->filled('buscar')) {
            $query->buscar($request->buscar);
        }

        if ($request->filled('tipo_membresia')) {
            $query->porTipo($request->tipo_membresia);
        }

        if ($request->filled('estado_membresia')) {
            $query->where('estado_membresia', $request->estado_membresia);
        }

        if ($request->filled('organizacion_id')) {
            $query->porOrganizacion($request->organizacion_id);
        }

        $miembros = $query->paginate(20);

        // Estadísticas para filtros
        $estadisticas = [
            'total_miembros' => Miembro::count(),
            'miembros_activos' => Miembro::activos()->count(),
            'por_tipo' => Miembro::select('tipo_membresia', DB::raw('count(*) as cantidad'))
                ->groupBy('tipo_membresia')
                ->get(),
            'por_estado' => Miembro::select('estado_membresia', DB::raw('count(*) as cantidad'))
                ->groupBy('estado_membresia')
                ->get(),
        ];

        $organizaciones = Organizacion::activas()->get();

        return view('miembros.index', compact(
            'miembros',
            'estadisticas',
            'organizaciones'
        ));
    }

    /**
     * Display the specified member.
     */
    public function show($id)
    {
        $miembro = Miembro::with([
            'organizacion',
            'miembrosDirectivos.cargo',
            'miembrosDirectivos.organo',
            'asistenciaAsambleas.asamblea',
            'inscripcionesCursos.curso'
        ])->findOrFail($id);

        $estadisticas = $miembro->estadisticas;
        $cargosActuales = $miembro->cargos_actuales;

        return view('miembros.show', compact('miembro', 'estadisticas', 'cargosActuales'));
    }

    /**
     * Generate member card (QR Code)
     */
    public function carnet($id)
    {
        $miembro = Miembro::with('organizacion')->findOrFail($id);
        
        // Generar datos para el carnet
        $carnetData = [
            'nombre' => $miembro->nombre_completo,
            'numero_carnet' => $miembro->numero_carnet,
            'organizacion' => $miembro->organizacion->nombre,
            'tipo_membresia' => $miembro->tipo_membresia,
            'fecha_ingreso' => $miembro->fecha_ingreso->format('Y'),
            'foto' => $miembro->foto_url,
        ];

        return view('miembros.carnet', compact('miembro', 'carnetData'));
    }

    /**
     * Get members data for API
     */
    public function api(Request $request)
    {
        $query = Miembro::with('organizacion');

        // Aplicar filtros
        if ($request->filled('buscar')) {
            $query->buscar($request->buscar);
        }

        if ($request->filled('tipo_membresia')) {
            $query->porTipo($request->tipo_membresia);
        }

        if ($request->filled('estado_membresia')) {
            $query->where('estado_membresia', $request->estado_membresia);
        }

        if ($request->filled('organizacion_id')) {
            $query->porOrganizacion($request->organizacion_id);
        }

        $miembros = $query->select([
            'id',
            'nombre_completo',
            'email',
            'numero_carnet',
            'tipo_membresia',
            'estado_membresia',
            'fecha_ingreso',
            'foto_url',
            'organizacion_id'
        ])->paginate($request->get('per_page', 20));

        return response()->json($miembros);
    }

    /**
     * Get member statistics
     */
    public function estadisticas()
    {
        $estadisticas = [
            'total_miembros' => Miembro::count(),
            'miembros_activos' => Miembro::activos()->count(),
            'miembros_suspendidos' => Miembro::where('estado_membresia', 'suspendida')->count(),
            'miembros_inactivos' => Miembro::where('estado_membresia', 'inactiva')->count(),
            'por_tipo' => Miembro::select('tipo_membresia', DB::raw('count(*) as cantidad'))
                ->groupBy('tipo_membresia')
                ->get(),
            'por_organizacion' => Miembro::with('organizacion')
                ->select('organizacion_id', DB::raw('count(*) as cantidad'))
                ->groupBy('organizacion_id')
                ->get(),
            'nuevos_este_mes' => Miembro::whereMonth('fecha_ingreso', now()->month)
                ->whereYear('fecha_ingreso', now()->year)
                ->count(),
        ];

        return response()->json($estadisticas);
    }

    /**
     * Search members
     */
    public function buscar(Request $request)
    {
        $termino = $request->get('q', '');
        
        if (strlen($termino) < 2) {
            return response()->json([]);
        }

        $miembros = Miembro::buscar($termino)
            ->with('organizacion')
            ->select([
                'id',
                'nombre_completo',
                'numero_carnet',
                'tipo_membresia',
                'estado_membresia',
                'foto_url',
                'organizacion_id'
            ])
            ->limit(10)
            ->get();

        return response()->json($miembros);
    }

    /**
     * Export members to CSV
     */
    public function exportar(Request $request)
    {
        $query = Miembro::with('organizacion');

        // Aplicar filtros
        if ($request->filled('tipo_membresia')) {
            $query->porTipo($request->tipo_membresia);
        }

        if ($request->filled('estado_membresia')) {
            $query->where('estado_membresia', $request->estado_membresia);
        }

        if ($request->filled('organizacion_id')) {
            $query->porOrganizacion($request->organizacion_id);
        }

        $miembros = $query->get();

        $filename = 'miembros_cldci_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($miembros) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, [
                'Número Carnet',
                'Nombre Completo',
                'Email',
                'Teléfono',
                'Profesión',
                'Tipo Membresía',
                'Estado',
                'Fecha Ingreso',
                'Organización',
                'Cédula'
            ]);

            // Data
            foreach ($miembros as $miembro) {
                fputcsv($file, [
                    $miembro->numero_carnet,
                    $miembro->nombre_completo,
                    $miembro->email,
                    $miembro->telefono,
                    $miembro->profesion,
                    $miembro->tipo_membresia,
                    $miembro->estado_membresia,
                    $miembro->fecha_ingreso->format('d/m/Y'),
                    $miembro->organizacion->nombre,
                    $miembro->cedula
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
