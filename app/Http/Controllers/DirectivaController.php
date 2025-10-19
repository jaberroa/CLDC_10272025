<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrganoCldc;
use App\Models\MiembroDirectivo;
use App\Models\Cargo;
use App\Models\Miembro;
use App\Models\Organizacion;
use Illuminate\Support\Facades\DB;

class DirectivaController extends Controller
{
    /**
     * Display the organizational structure.
     */
    public function index()
    {
        // Obtener estructura organizacional
        $organos = OrganoCldc::with([
            'miembrosDirectivos.miembro',
            'miembrosDirectivos.cargo'
        ])->orderBy('nivel_jerarquico')->get();

        // Agrupar por nivel jerárquico
        $estructura = $organos->groupBy('nivel_jerarquico');

        // Obtener estadísticas
        $estadisticas = [
            'total_organos' => $organos->count(),
            'total_directivos' => MiembroDirectivo::where('estado', 'activo')->count(),
            'presidentes' => MiembroDirectivo::where('estado', 'activo')
                ->where('es_presidente', true)
                ->count(),
            'por_nivel' => $organos->groupBy('nivel_jerarquico')->map->count(),
        ];

        // Obtener cargos disponibles
        $cargos = Cargo::ordenados()->get();

        // Obtener miembros disponibles para asignar cargos
        $miembrosDisponibles = Miembro::activos()
            ->with('organizacion')
            ->get();

        return view('directiva.index', compact(
            'estructura',
            'estadisticas',
            'cargos',
            'miembrosDisponibles'
        ));
    }

    /**
     * Get organizational chart data
     */
    public function organigrama()
    {
        $organos = OrganoCldc::with([
            'miembrosDirectivos.miembro',
            'miembrosDirectivos.cargo'
        ])->orderBy('nivel_jerarquico')->get();

        $organigrama = [];
        
        foreach ($organos as $organo) {
            $nodo = [
                'id' => $organo->id,
                'nombre' => $organo->nombre,
                'tipo' => $organo->tipo,
                'nivel' => $organo->nivel_jerarquico,
                'miembros' => $organo->miembrosDirectivos->map(function ($miembro) {
                    return [
                        'id' => $miembro->id,
                        'nombre' => $miembro->miembro->nombre_completo,
                        'cargo' => $miembro->cargo->nombre,
                        'es_presidente' => $miembro->es_presidente,
                        'foto' => $miembro->miembro->foto_url,
                    ];
                })
            ];
            
            $organigrama[] = $nodo;
        }

        return response()->json($organigrama);
    }

    /**
     * Get members by organization
     */
    public function miembrosOrgano($organoId)
    {
        $organo = OrganoCldc::with([
            'miembrosDirectivos.miembro.organizacion',
            'miembrosDirectivos.cargo'
        ])->findOrFail($organoId);

        $miembros = $organo->miembrosDirectivos()
            ->where('estado', 'activo')
            ->with(['miembro.organizacion', 'cargo'])
            ->get();

        return response()->json([
            'organo' => $organo,
            'miembros' => $miembros
        ]);
    }

    /**
     * Get member details
     */
    public function miembroDetalle($miembroDirectivoId)
    {
        $miembroDirectivo = MiembroDirectivo::with([
            'miembro.organizacion',
            'cargo',
            'organo'
        ])->findOrFail($miembroDirectivoId);

        return response()->json($miembroDirectivo);
    }

    /**
     * Get statistics for dashboard
     */
    public function estadisticas()
    {
        $estadisticas = [
            'total_organos' => OrganoCldc::count(),
            'total_directivos' => MiembroDirectivo::where('estado', 'activo')->count(),
            'presidentes' => MiembroDirectivo::where('estado', 'activo')
                ->where('es_presidente', true)
                ->count(),
            'por_tipo_organo' => OrganoCldc::select('tipo', DB::raw('count(*) as cantidad'))
                ->groupBy('tipo')
                ->get(),
            'por_cargo' => MiembroDirectivo::where('estado', 'activo')
                ->with('cargo')
                ->get()
                ->groupBy('cargo.nombre')
                ->map->count(),
            'por_organizacion' => MiembroDirectivo::where('estado', 'activo')
                ->with('organo.organizacion')
                ->get()
                ->groupBy('organo.organizacion.nombre')
                ->map->count(),
        ];

        return response()->json($estadisticas);
    }

    /**
     * Get timeline of changes
     */
    public function timeline()
    {
        $cambios = MiembroDirectivo::with([
            'miembro',
            'cargo',
            'organo'
        ])
        ->orderBy('created_at', 'desc')
        ->limit(20)
        ->get()
        ->map(function ($item) {
            return [
                'id' => $item->id,
                'fecha' => $item->created_at->format('d/m/Y H:i'),
                'miembro' => $item->miembro->nombre_completo,
                'cargo' => $item->cargo->nombre,
                'organo' => $item->organo->nombre,
                'accion' => $item->estado === 'activo' ? 'Asignado' : 'Removido',
                'es_presidente' => $item->es_presidente,
            ];
        });

        return response()->json($cambios);
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

        $miembros = MiembroDirectivo::where('estado', 'activo')
            ->whereHas('miembro', function ($query) use ($termino) {
                $query->where('nombre_completo', 'like', "%{$termino}%")
                      ->orWhere('numero_carnet', 'like', "%{$termino}%");
            })
            ->with(['miembro', 'cargo', 'organo'])
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nombre' => $item->miembro->nombre_completo,
                    'cargo' => $item->cargo->nombre,
                    'organo' => $item->organo->nombre,
                    'foto' => $item->miembro->foto_url,
                ];
            });

        return response()->json($miembros);
    }

    /**
     * Export organizational structure
     */
    public function exportar()
    {
        $organos = OrganoCldc::with([
            'miembrosDirectivos.miembro',
            'miembrosDirectivos.cargo'
        ])->orderBy('nivel_jerarquico')->get();

        $filename = 'estructura_directiva_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($organos) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, [
                'Órgano',
                'Tipo',
                'Nivel',
                'Miembro',
                'Cargo',
                'Es Presidente',
                'Fecha Inicio',
                'Fecha Fin',
                'Estado'
            ]);

            // Data
            foreach ($organos as $organo) {
                foreach ($organo->miembrosDirectivos as $miembroDirectivo) {
                    fputcsv($file, [
                        $organo->nombre,
                        $organo->tipo,
                        $organo->nivel_jerarquico,
                        $miembroDirectivo->miembro->nombre_completo,
                        $miembroDirectivo->cargo->nombre,
                        $miembroDirectivo->es_presidente ? 'Sí' : 'No',
                        $miembroDirectivo->fecha_inicio->format('d/m/Y'),
                        $miembroDirectivo->fecha_fin ? $miembroDirectivo->fecha_fin->format('d/m/Y') : 'Activo',
                        $miembroDirectivo->estado,
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

