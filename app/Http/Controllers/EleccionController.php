<?php

namespace App\Http\Controllers;

use App\Models\Eleccion;
use App\Models\Organizacion;
use Illuminate\Http\Request;

class EleccionController extends Controller
{
    public function index()
    {
        $elecciones = Eleccion::with(['organizacion', 'votos'])
            ->orderBy('fecha_inicio', 'desc')
            ->paginate(10);

        $estadisticas = [
            'total_elecciones' => Eleccion::count(),
            'elecciones_activas' => Eleccion::where('estado', 'activa')->count(),
            'proximas_elecciones' => Eleccion::where('estado', 'programada')
                ->where('fecha_inicio', '>', now())
                ->count(),
            'votos_totales' => Eleccion::has('votos')->withCount('votos')->get()->sum('votos_count'),
        ];

        $proximasElecciones = Eleccion::with('organizacion')
            ->where('estado', 'programada')
            ->where('fecha_inicio', '>', now())
            ->orderBy('fecha_inicio', 'asc')
            ->limit(5)
            ->get();

        return view('elecciones.index', compact('elecciones', 'estadisticas', 'proximasElecciones'));
    }

    public function candidatos()
    {
        return view('elecciones.candidatos');
    }

    public function votacion()
    {
        return view('elecciones.votacion');
    }

    /**
     * Mostrar resultados de una elección
     * Solo accesible cuando la elección ha finalizado
     */
    public function results($id)
    {
        $eleccion = Eleccion::with(['candidatos', 'organizacion'])->findOrFail($id);

        // Por ahora, mostrar resultados en tiempo real
        // TODO: En producción, verificar que la elección haya finalizado
        
        $estadisticas = [
            'total_votos' => $eleccion->votos()->count() ?? 0,
            'total_candidatos' => $eleccion->candidatos()->count() ?? 0,
            'participacion' => 0, // Calcular según votantes elegibles
        ];

        // Obtener resultados por candidato
        $resultados = $eleccion->candidatos->map(function($candidato) {
            return [
                'candidato' => $candidato->miembro->nombre_completo ?? 'N/A',
                'cargo' => $candidato->cargo->nombre ?? 'N/A',
                'votos' => $candidato->votos_recibidos ?? 0,
                'porcentaje' => 0, // Calcular después
            ];
        });

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'eleccion' => [
                    'id' => $eleccion->id,
                    'titulo' => $eleccion->titulo,
                    'descripcion' => $eleccion->descripcion,
                ],
                'resultados' => $resultados,
                'estadisticas' => $estadisticas,
            ]);
        }

        return view('elecciones.resultados', compact('eleccion', 'resultados', 'estadisticas'));
    }

    /**
     * Verificar estado de votación del usuario
     */
    public function verificarEstado($id)
    {
        $eleccion = Eleccion::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'eleccion_activa' => $eleccion->estaActiva(),
            'ya_voto' => $eleccion->usuarioYaVoto(auth()->id()),
            'puede_votar' => $eleccion->estaActiva() && !$eleccion->usuarioYaVoto(auth()->id()),
        ]);
    }
}

